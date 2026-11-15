#!/usr/bin/env bash
# Emite o certificado de um servidor com a CA interna. Roda no PC (WSL), onde esta a CA.
# Pede a senha da CA. Gera a chave do servidor e o certificado (valido por 2 anos: limite
# aceito pelos navegadores da Apple; emitir de novo antes de vencer).
#
# Uso: deploy/https/emitir-certificado.sh <nome> <ip> [dominio ...]
#   ex.: deploy/https/emitir-certificado.sh europa-hd 172.31.20.60
set -euo pipefail

if [ $# -lt 2 ]; then
    echo "Uso: $0 <nome> <ip> [dominio ...]   (ex.: $0 europa-hd 172.31.20.60)" >&2
    exit 2
fi
NOME="$1"; IP="$2"; shift 2
[[ "$NOME" =~ ^[a-z0-9-]+$ ]] || { echo "Nome so com letras minusculas, numeros e hifen." >&2; exit 2; }
[[ "$IP" =~ ^[0-9]{1,3}(\.[0-9]{1,3}){3}$ ]] || { echo "IP invalido: $IP" >&2; exit 2; }

CA_DIR="${CA_DIR:-$HOME/.config/bvaa/ca}"
DIAS="${DIAS:-730}"
[ -e "$CA_DIR/bvaa-ca.key" ] || { echo "CA nao encontrada em $CA_DIR (rodar deploy/https/criar-ca.sh)." >&2; exit 1; }

DIR="$CA_DIR/servidores/$NOME"
install -d -m 700 "$DIR"
umask 077
STAMP="$(date +%Y%m%d)"
CNF="$(mktemp "$DIR/.cert-XXXXXX.cnf")"
CSR="$(mktemp "$DIR/.csr-XXXXXX.pem")"
trap 'rm -f "$CNF" "$CSR"' EXIT

SAN="IP:$IP"
for d in "$@"; do SAN="$SAN,DNS:$d"; done

cat > "$CNF" <<CNF
[req]
distinguished_name = dn
prompt = no

[dn]
O = BVAA
CN = $NOME

[srv]
basicConstraints = critical, CA:FALSE
keyUsage = critical, digitalSignature
extendedKeyUsage = serverAuth
subjectAltName = $SAN
subjectKeyIdentifier = hash
authorityKeyIdentifier = keyid
CNF

# Chave nova a cada emissao. Emite em arquivos temporarios e so troca os definitivos depois de
# conferir (a versao anterior fica guardada com a data, para voltar atras).
NEW_KEY="$DIR/.novo-$NOME.key"; NEW_CRT="$DIR/.novo-$NOME.crt"
trap 'rm -f "$CNF" "$CSR" "$NEW_KEY" "$NEW_CRT"' EXIT
openssl genpkey -algorithm EC -pkeyopt ec_paramgen_curve:P-256 -out "$NEW_KEY"
openssl req -new -config "$CNF" -key "$NEW_KEY" -out "$CSR"
echo "Senha da CA:"
openssl x509 -req -in "$CSR" -CA "$CA_DIR/bvaa-ca.crt" -CAkey "$CA_DIR/bvaa-ca.key" \
    ${CA_PASS_FILE:+-passin "file:$CA_PASS_FILE"} \
    -set_serial "0x$(openssl rand -hex 16)" -days "$DIAS" -sha256 \
    -extfile "$CNF" -extensions srv -out "$NEW_CRT"

# Confere a cadeia, o uso e o IP (inclusive as restricoes da CA: IP publico ou dominio de fora falham).
if ! openssl verify -CAfile "$CA_DIR/bvaa-ca.crt" -verify_ip "$IP" -purpose sslserver "$NEW_CRT" >/dev/null; then
    echo "Certificado recusado na conferencia (nada foi trocado)." >&2
    exit 1
fi
for f in "$NOME.key" "$NOME.crt"; do
    [ -e "$DIR/$f" ] && mv "$DIR/$f" "$DIR/$f.ate-$STAMP"
done
mv "$NEW_KEY" "$DIR/$NOME.key"
mv "$NEW_CRT" "$DIR/$NOME.crt"
chmod 644 "$DIR/$NOME.crt"

echo
echo "Certificado emitido e conferido:"
openssl x509 -in "$DIR/$NOME.crt" -noout -subject -enddate -ext subjectAltName
echo
echo "Copiar para o servidor (do PC):"
echo "  scp \"$DIR/$NOME.crt\" \"$DIR/$NOME.key\" \"$CA_DIR/bvaa-ca.crt\" fabio.ferreira@$IP:~"
echo "e instalar como em deploy/https/README.md (secao 2)."
