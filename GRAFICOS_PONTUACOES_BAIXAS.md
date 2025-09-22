# Gráfico de Pontuações Baixas - Sistema Escolar

## Visão Geral

Este recurso adiciona um gráfico ao dashboard que mostra estudantes com pontuações baixas (necessitando de recuperação) organizados por turma.

## Funcionalidades Implementadas

### 1. Interface de Repository

- **Arquivo**: `src/Interfaces/Scores/ILowScoresRepository.php`
- **Métodos**:
  - `getLowScoresByClass()`: Obtém estatísticas por turma
  - `getStudentsWithLowScores()`: Obtém detalhes dos estudantes
  - `getLowScoresStatistics()`: Obtém estatísticas gerais

### 2. Repository

- **Arquivo**: `src/Repositories/Scores/LowScoresRepository.php`
- **Funcionalidades**:
  - Consulta estudantes com notas finais abaixo de 6.0 (configurável)
  - Agrupa por turma e conta quantidade de estudantes
  - Retorna dados formatados para ApexCharts
  - Tratamento de erros e logging

### 3. Controller

- **Arquivo**: `src/Controllers/v1/Dashboard/DashboardController.php`
- **Novos métodos**:
  - `getLowScoresChart()`: API para dados do gráfico
  - `getLowScoresDetails()`: API para detalhes dos estudantes
  - `getLowScoresStatistics()`: API para estatísticas gerais

### 4. Rotas

- **Arquivo**: `src/Routers/v1/dashboard/dashboardRouters.php`
- **Novas rotas**:
  - `GET /dashboard/low-scores-chart`
  - `GET /dashboard/low-scores-details`
  - `GET /dashboard/low-scores-statistics`

### 5. Frontend

- **Arquivo**: `src/Resources/Views/dashboard/index.php`
- **Adicionado**:
  - Novo card com gráfico de barras
  - JavaScript para carregar dados via API
  - Gráfico ApexCharts responsivo

## Como Usar

### Parâmetros de Configuração

Você pode personalizar o gráfico através de parâmetros na URL:

- `ano_letivo`: Ano letivo para consulta (padrão: ano atual)
- `limite_nota`: Nota mínima para não precisar de recuperação (padrão: 6.0)

### Exemplos de URLs

```
/dashboard/low-scores-chart?ano_letivo=2025&limite_nota=6.0
/dashboard/low-scores-details?turma_id=1&ano_letivo=2025
/dashboard/low-scores-statistics?ano_letivo=2025
```

### Estrutura do Banco de Dados

O recurso utiliza as seguintes tabelas:

- `turmas`: Informações das turmas
- `estudante_turma`: Relacionamento estudante-turma
- `nota_final`: Notas finais dos estudantes
- `turma_disciplina`: Relacionamento turma-disciplina
- `estudantes`: Dados dos estudantes
- `pessoa_fisica`: Dados pessoais

## Características do Gráfico

- **Tipo**: Gráfico de barras vertical
- **Título**: "Pontuações baixas"
- **Dados**: Quantidade de estudantes por turma
- **Cor**: Vermelho (#ff6b6b) para destacar a necessidade de atenção
- **Interatividade**: Tooltip mostra número exato de estudantes
- **Responsivo**: Adapta-se a diferentes tamanhos de tela

## Personalização

### Alterar Limite de Nota

Para alterar o limite padrão de 6.0, modifique o parâmetro `limite_nota` nas chamadas da API.

### Alterar Aparência do Gráfico

Modifique as opções do ApexCharts no arquivo `dashboard/index.php`:

- `colors`: Alterar cores
- `chart.height`: Alterar altura
- `plotOptions.bar`: Configurações das barras

### Adicionar Filtros

Você pode estender os métodos do repository para adicionar filtros por:

- Período específico
- Disciplina
- Professor
- Turno

## Segurança

- Todas as rotas requerem autenticação (`$auth`)
- Parâmetros são validados e sanitizados
- Consultas SQL utilizam prepared statements
- Tratamento de erros com logging

## Performance

- Consultas otimizadas com JOINs apropriados
- Dados agregados no banco de dados
- Cache pode ser implementado facilmente
- Paginação disponível para detalhes

## Manutenção

### Logs

Erros são registrados via `LoggerHelper::logInfo()` no arquivo de log configurado.

### Monitoramento

Monitore as seguintes métricas:

- Tempo de resposta das APIs
- Número de estudantes em recuperação
- Performance das consultas SQL

## Expansões Futuras

Possíveis melhorias:

1. Dashboard específico para coordenadores
2. Alertas automáticos para muitos estudantes em recuperação
3. Relatórios exportáveis (PDF/Excel)
4. Gráficos adicionais (tendências, comparativos)
5. Filtros avançados na interface
6. Notificações para pais/responsáveis
