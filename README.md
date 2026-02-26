## Coisas Utilizadas até agora:

### Honey Pot e Throttle para evitar spam.

### Recaptcha v3 (google atribui um score para o usuario com base no comportamento e devolve se ta ok)

### Envio de email automatico com retry apos envio do cadastro lead.

### Evita envio de emails duplicados, se ja existir apenas atualiza o updated, e adiciona multiplas origins.

### Log de auditoria agora para cada LEAD criado, atualizado e excluido (futuramente podendo ser colocado tambem ao gerar relatorio) utilizando spatie/laravel-activitylog

### Adicionado tabela roles para controle e gerenciamento de usuarios do sistema.

### Criado dashboard para visualização rapida dos Leads.

### Criado pagina de leads com filtros de data, email e origem.

### Criado exportação de leads com notificação via email para casos de leads em grandes volumes.