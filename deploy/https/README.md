# HTTPS com a CA interna do BVAA

Os servidores ficam na rede interna, acessados pelo IP (`172.31.20.60`), e nenhuma autoridade
pública (Let's Encrypt ou paga) emite certificado para IP privado. Por isso usamos uma **CA
interna** (decisão do Fabio em 15/11/2026): uma raiz própria, instalada uma vez nos computadores
da empresa. Com ela, o navegador mostra o cadeado normal, sem aviso.

| Arquivo | Para quê |
|---|---|
| `criar-ca.sh` | cria a CA (uma vez só), no PC |
| `emitir-certificado.sh` | emite o certificado de um servidor, no PC |
| `nginx-https.conf` | `/etc/nginx/snippets/peticaofacil-extra/00-https.conf` no EUROPA-HD |

**O certificado vale para o site inteiro do EUROPA-HD**: Petição Fácil (raiz), `/bvtarefas`,
`/bvacesso` e `/ars`. O arquivo do nginx entra no diretório que o site do Petição Fácil já inclui
dentro do seu `server`, então a porta 443 atende com as mesmas regras da 80, sem editar o site.

**A CA só vale para a rede interna**: IPs `10.x`, `172.16–31.x` e `192.168.x`, e os domínios
`bvaa.adv.br` e `brunovanderlei.adv.br` (`nameConstraints`). Se a chave vazar, ela não serve para
imitar outros sites (bancos, Google…). A chave fica no PC do Fabio, protegida por senha, nunca nos
servidores.

## 0. Antes (TI)

- Liberar a **porta 443** da rede dos usuários até o `172.31.20.60` (hoje só passam a 22 e a 80).
- Se houver **Active Directory**: distribuir a raiz por GPO (seção 3). Sem AD, a raiz é
  instalada à mão em cada computador.

## 1. Criar a CA e o certificado (PC, WSL, em `~/projetos/ars`)

```bash
deploy/https/criar-ca.sh
```

Pede uma senha forte para a chave da CA. Ela será pedida em cada certificado emitido. Guarde
`~/.config/bvaa/ca/bvaa-ca.key` **e** a senha fora do PC (cofre da TI ou pendrive guardado): sem
elas não dá para renovar. O script mostra a impressão digital (SHA-256) da raiz: anote, para conferir
nos computadores.

```bash
deploy/https/emitir-certificado.sh europa-hd 172.31.20.60
```

Gera a chave e o certificado do EUROPA-HD em `~/.config/bvaa/ca/servidores/europa-hd/`, válido por
2 anos, e confere a cadeia e o IP. No fim mostra o `scp` para o servidor.

## 2. Instalar no EUROPA-HD (fase 1: HTTPS ao lado do HTTP)

Nada muda para quem usa: o HTTP continua igual. Do PC, copiar os arquivos (o `scp` mostrado no
passo 1):

```bash
scp ~/.config/bvaa/ca/servidores/europa-hd/europa-hd.crt ~/.config/bvaa/ca/servidores/europa-hd/europa-hd.key ~/.config/bvaa/ca/bvaa-ca.crt fabio.ferreira@172.31.20.60:~
```

No EUROPA-HD:

```bash
sudo install -d -m 755 /etc/ssl/bvaa && sudo install -d -m 700 /etc/ssl/bvaa/private
sudo install -m 644 ~/europa-hd.crt /etc/ssl/bvaa/europa-hd.crt
sudo install -m 600 ~/europa-hd.key /etc/ssl/bvaa/private/europa-hd.key
# O próprio servidor passa a confiar na CA (os sistemas falam com o BVAcesso por https na fase 3).
sudo install -m 644 ~/bvaa-ca.crt /usr/local/share/ca-certificates/bvaa-ca.crt && sudo update-ca-certificates
rm ~/europa-hd.crt ~/europa-hd.key ~/bvaa-ca.crt

cd /var/www/ars && ./deploy/atualizar.sh        # traz o deploy/https/ do GitHub
sudo cp deploy/https/nginx-https.conf /etc/nginx/snippets/peticaofacil-extra/00-https.conf
sudo nginx -t && sudo systemctl reload nginx
sudo ufw status | grep -q 'Status: active' && sudo ufw allow 443/tcp   # só se o firewall do Ubuntu estiver ligado

curl -sS -o /dev/null -w '%{http_code} verificacao=%{ssl_verify_result}\n' https://172.31.20.60/ars/login   # 200 verificacao=0
```

Do PC (confere se a rede deixa passar a 443):

```bash
curl -sS -o /dev/null --cacert ~/.config/bvaa/ca/bvaa-ca.crt -w '%{http_code}\n' https://172.31.20.60/ars/login   # 200
```

Desfazer: `sudo rm /etc/nginx/snippets/peticaofacil-extra/00-https.conf && sudo nginx -t && sudo systemctl reload nginx`.

## 3. Instalar a raiz nos computadores

O arquivo `bvaa-ca.crt` é público (só a chave é secreta). Conferir a impressão digital SHA-256 com a
anotada no passo 1.

- **Por GPO (TI, com AD):** Configuração do Computador → Políticas → Configurações do Windows →
  Configurações de Segurança → Políticas de Chave Pública → Autoridades de Certificação Raiz
  Confiáveis → Importar `bvaa-ca.crt`.
- **À mão (Windows, como administrador):** `certutil -addstore -f Root bvaa-ca.crt`, ou duplo clique
  no arquivo → Instalar Certificado → Máquina Local → "Autoridades de Certificação Raiz Confiáveis".
- Chrome e Edge usam o repositório do Windows. O Firefox também, nas versões atuais
  (`security.enterprise_roots.enabled`). Fechar e abrir o navegador depois de instalar.

Teste: `https://172.31.20.60/ars` com cadeado e sem aviso. Até a fase 3, quem não tem a raiz continua
usando o `http://` sem perceber nada.

## 4. Virada (fase 3: tudo em HTTPS)

Só depois de a raiz estar nos computadores. Fazer tudo de uma vez, num horário de pouco uso.

1. **BVBots (MIMAS-HD) confiando na CA.** O container dele chama o BVAcesso por trás (troca do
   código e conferência da sessão). Sem a raiz dentro do container, o login pelo BVAcesso no BVBots
   para. O ajuste fica no repositório do BVBots: a raiz no `Dockerfile` (`update-ca-certificates`)
   e `BVACESSO_URL=https://172.31.20.60/bvacesso` no `deploy/bvbots.env`. Pendente.
2. **BVAcesso** → Administração → Sistemas: em Petição Fácil, BVTarefas e ARS, trocar o endereço
   e o retorno para `https://` (ex.: `https://172.31.20.60/ars/auth/bvacesso/callback`).
3. **Os quatro `.env`** do EUROPA-HD (`/var/www/peticaofacil`, `bvtarefas`, `bvacesso`, `ars`): o
   `APP_URL` e o `BVACESSO_URL` passam a `https://`, e os cookies passam a ser só HTTPS. Em cada um:

   ```bash
   cp .env .env.antes-https && sed -i 's#^APP_URL=http://#APP_URL=https://#; s#^BVACESSO_URL=http://#BVACESSO_URL=https://#' .env
   grep -q '^SESSION_SECURE_COOKIE=' .env && sed -i 's/^SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=true/' .env || echo 'SESSION_SECURE_COOKIE=true' >> .env
   grep -E '^(APP_URL|BVACESSO_URL|SESSION_SECURE_COOKIE)=' .env && php artisan optimize
   ```

4. **Redirecionar o HTTP:** em `/etc/nginx/snippets/peticaofacil-extra/00-https.conf`, descomentar o
   bloco `if ($scheme = http)` (308, mantém o POST), depois `sudo nginx -t && sudo systemctl reload nginx`.
5. **Servidor antigo (10.81.11.202):** nas regras `ars-redirect.conf` e `peticaofacil2-redirect.conf`,
   trocar `http://172.31.20.60` por `https://172.31.20.60` (evita um salto a mais).
6. Conferir: login nos quatro sistemas, inclusive pelo BVAcesso; painel do NEO na ARS; PDF no
   Petição Fácil. Sessões abertas antes da virada pedem login de novo (cookie novo).

Voltar atrás: comentar de novo o redirecionamento e recarregar o nginx, voltar os
`.env.antes-https` (`php artisan optimize`) e os endereços no BVAcesso.

## 5. Renovar (a cada 2 anos)

```bash
openssl x509 -enddate -noout -in /etc/ssl/bvaa/europa-hd.crt     # no servidor: data de vencimento
```

Um mês antes: `deploy/https/emitir-certificado.sh europa-hd 172.31.20.60` no PC, depois o `scp` e as
linhas `install … europa-hd.*` da seção 2 e `sudo systemctl reload nginx`. A raiz dos computadores
não muda (vale 10 anos). A versão anterior fica no PC com a data no nome.

## 6. Outros servidores

O mesmo `emitir-certificado.sh <nome> <ip>` serve para o MIMAS-HD (BVBots): copiar o certificado e a
chave como `deploy/certs/certificado.pem` e `chave.pem` e usar
`CADDY_TLS="/certs/certificado.pem /certs/chave.pem"` (ver o `deploy/README.md` do BVBots).

Chave da CA perdida ou vazada: criar uma CA nova, reinstalar a raiz nos computadores e emitir de
novo os certificados (não há lista de revogação).
