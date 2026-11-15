#!/usr/bin/env bash
# Cria a CA interna do BVAA (uma vez só). Roda no PC (WSL), nunca no servidor.
# A chave da CA fica em ~/.config/bvaa/ca, protegida por senha: guardar copia de
# bvaa-ca.key + senha fora do PC (cofre da TI ou pendrive guardado). Quem tem as duas
# emite certificado aceito por todos os computadores da empresa.
#
# A CA so vale para IPs da rede interna e para os dominios bvaa.adv.br e
# brunovanderlei.adv.br (nameConstraints): se a chave vazar, nao serve para imitar
# outros sites (bancos, Google...).
#
# Uso: deploy/https/criar-ca.sh
set -euo pipefail

CA_DIR="${CA_DIR:-$HOME/.config/bvaa/ca}"
CA_NOME="${CA_NOME:-BVAA CA Interna}"
CA_DIAS="${CA_DIAS:-3650}"
# So para testes automatizados: arquivo com a senha (sem ele, a senha e pedida no terminal).
PASS_ARGS=()
if [ -n "${CA_PASS_FILE:-}" ]; then PASS_ARGS=(-pass "file:$CA_PASS_FILE"); fi

if [ -e "$CA_DIR/bvaa-ca.key" ] || [ -e "$CA_DIR/bvaa-ca.crt" ]; then
    echo "A CA ja existe em $CA_DIR. Nada foi feito (apagar a CA invalida todos os certificados emitidos)." >&2
    exit 1
fi

install -d -m 700 "$CA_DIR" "$CA_DIR/servidores"
umask 077
CNF="$(mktemp "$CA_DIR/.ca-XXXXXX.cnf")"
trap 'rm -f "$CNF"' EXIT

cat > "$CNF" <<CNF
[req]
distinguished_name = dn
prompt = no
x509_extensions = v3_ca

[dn]
C = BR
O = BVAA
CN = $CA_NOME

[v3_ca]
basicConstraints = critical, CA:TRUE, pathlen:0
keyUsage = critical, keyCertSign, cRLSign
subjectKeyIdentifier = hash
nameConstraints = critical, @nc

[nc]
permitted;IP.1 = 10.0.0.0/255.0.0.0
permitted;IP.2 = 172.16.0.0/255.240.0.0
permitted;IP.3 = 192.168.0.0/255.255.0.0
permitted;DNS.1 = bvaa.adv.br
permitted;DNS.2 = brunovanderlei.adv.br
CNF

echo "Senha da chave da CA (forte, guardada fora do PC; sera pedida a cada certificado emitido):"
openssl genpkey -algorithm EC -pkeyopt ec_paramgen_curve:P-384 -aes-256-cbc "${PASS_ARGS[@]}" -out "$CA_DIR/bvaa-ca.key"
openssl req -x509 -new -config "$CNF" -key "$CA_DIR/bvaa-ca.key" ${CA_PASS_FILE:+-passin "file:$CA_PASS_FILE"} \
    -sha384 -days "$CA_DIAS" -set_serial "0x$(openssl rand -hex 16)" -out "$CA_DIR/bvaa-ca.crt"
chmod 644 "$CA_DIR/bvaa-ca.crt"

echo
echo "CA criada:"
openssl x509 -in "$CA_DIR/bvaa-ca.crt" -noout -subject -enddate -fingerprint -sha256
echo
echo "  Chave (secreta):   $CA_DIR/bvaa-ca.key   -> copia + senha fora do PC"
echo "  Certificado raiz:  $CA_DIR/bvaa-ca.crt   -> vai para os computadores e servidores (pode ser publico)"
echo "Proximo passo: deploy/https/emitir-certificado.sh europa-hd 172.31.20.60"
