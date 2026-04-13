<x-app-layout>
    <x-slot name="header">
        Visão Geral - {{ str_pad($competence->month, 2, '0', STR_PAD_LEFT) }}/{{ $competence->year }}

        <div>
            <span class="badge {{ $competence->status == 'current' ? 'text-bg-success' : ($competence->status == 'future' ? 'text-bg-warning' : 'text-bg-secondary') }} fs-6">
                Status: {{ strtoupper($competence->status) }}
            </span>
        </div>
    </x-slot>

    <div class="container-fluid">
        <!-- Navegação de Meses (Gavetas) -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                @foreach($allCompetences as $data)
                    <div class="col-sm-1">
                        <a href="{{ route('dashboard') . '?month=' . $data->month . '&year=' . $data->year }}" style="text-decoration: none">
                            <div class="info-box {{ (($competence->month == $data->month) && ($competence->year == $data->year) ? 'text-bg-success' : 'text-bg-light') }}">
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $data->month }}/{{ $data->year }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Cards de Resumo -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box text-bg-primary">
                    <span class="info-box-icon"><i class="bi bi-wallet2"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Receita Prevista</span>
                        <span class="info-box-number">R$ {{ number_format($competence->total_income_planned, 2, ',', '.') }}</span>
                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                        <span class="progress-description text-white-50">Realizado: R$ {{ number_format($competence->total_income_realized, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box text-bg-danger">
                    <span class="info-box-icon"><i class="bi bi-arrow-down-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Custo Previsto</span>
                        <span class="info-box-number">R$ {{ number_format($competence->total_expense_planned, 2, ',', '.') }}</span>
                        <div class="progress"><div class="progress-bar" style="width: 100%"></div></div>
                        <span class="progress-description text-white-50">Realizado: R$ {{ number_format($competence->total_expense_realized, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-4">
                <div class="info-box {{ ($competence->total_income_planned - $competence->total_expense_planned) >= 0 ? 'text-bg-success' : 'text-bg-warning' }}">
                    <span class="info-box-icon"><i class="bi bi-piggy-bank"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Projeção de Sobra</span>
                        <span class="info-box-number">R$ {{ number_format($competence->total_income_planned - $competence->total_expense_planned, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartões de Crédito e Faturas -->
        <h5 class="mt-4 mb-3 fw-bold"><i class="bi bi-credit-card-2-front me-2"></i> Limites e Faturas Deste Mês</h5>
        <div class="row">
            @foreach($cards as $card)
                @php
                    $percent = $card->limit > 0 ? ($card->used_limit / $card->limit) * 100 : 0;
                    $colorClass = $percent > 85 ? 'bg-danger' : ($percent > 60 ? 'bg-warning' : 'bg-success');

                    $thisInvoice = $invoices->where('credit_card_id', $card->id)->first();
                    $invoiceAmount = $thisInvoice ?
                        ($thisInvoice->transactions->where('status', 'pending')->sum('planned_amount') +
                         $thisInvoice->transactions->where('status', 'paid')->sum('realized_amount')) : 0;
                @endphp
                <div class="col-12 col-md-4">
                    <div class="card shadow-sm border-0" style="border-top: 4px solid {{ $card->color ?? '#000' }} !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">{{ $card->name }}</h6>
                                <span class="badge text-bg-light border">Venc. Dia {{ $card->due_day }}</span>
                            </div>

                            <p class="mb-1 text-muted small">Limite Usado (Total): R$ {{ number_format($card->used_limit, 2, ',', '.') }} / R$ {{ number_format($card->limit, 2, ',', '.') }}</p>
                            <div class="progress mb-3" style="height: 8px;">
                                <div class="progress-bar {{ $colorClass }}" style="width: {{ $percent }}%"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background-color: #f8f9fa;">
                                <div>
                                    <small class="text-muted d-block">Fatura deste mês:</small>
                                    <span class="fw-bold fs-5 text-danger">R$ {{ number_format($invoiceAmount, 2, ',', '.') }}</span>
                                </div>
                                <div>
                                    @if($thisInvoice && $thisInvoice->status == 'paid')
                                        <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> Paga</span>
                                        <form action="{{ route('invoices.unpay', $thisInvoice->id) }}" method="POST" class="d-inline ms-1">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Reabrir Fatura e Estornar Saldo" onclick="return confirm('Deseja reabrir esta fatura? O dinheiro será devolvido para o saldo do seu banco e as compras reabertas.')">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-outline-danger" {{ $invoiceAmount == 0 ? 'disabled' : '' }}
                                        onclick="openPayInvoiceModal({{ $thisInvoice->id ?? 0 }}, '{{ addslashes($card->name) }}', {{ $invoiceAmount }})">
                                            Pagar Fatura
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Tabela de Lançamentos -->
        <div class="card card-outline card-primary shadow mt-4">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-list-task me-2"></i> Lançamentos do Mês</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addTransactionModal" {{ $competence->status == 'consolidated' ? 'disabled' : '' }}>
                        <i class="bi bi-plus-lg"></i> Receita/Despesa Avulsa
                    </button>
                    <button class="btn btn-sm btn-warning ms-1" data-bs-toggle="modal" data-bs-target="#installmentModal" {{ $competence->status == 'consolidated' ? 'disabled' : '' }}>
                        <i class="bi bi-credit-card"></i> Compra no Cartão
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr class="text-center">
                            <th>Vencimento</th>
                            <th class="text-start">Descrição</th>
                            <th>Tipo</th>
                            <th>Valor Planejado</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td class="text-center">{{ $t->due_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="fw-bold">{{ $t->description }}</span>
                                    @if($t->is_fixed)
                                        <span class="badge text-bg-info ms-1"><i class="bi bi-arrow-repeat"></i> Fixo</span>
                                    @endif

                                    @if($t->installment_total)
                                        <span class="badge text-bg-warning text-dark ms-1">{{ $t->installment_current }}/{{ $t->installment_total }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($t->type == 'income')
                                        <span class="text-success"><i class="bi bi-arrow-up"></i> Receita</span>
                                    @elseif($t->type == 'expense')
                                        <span class="text-danger"><i class="bi bi-arrow-down"></i> Despesa</span>
                                    @else
                                        <span class="text-primary"><i class="bi bi-arrow-left-right"></i> Transf.</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-muted text-center">R$ {{ number_format($t->planned_amount, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <!-- AVISO DE STATUS CORRETO -->
                                    @if($t->status == 'paid')
                                        <span class="badge text-bg-success">Pago</span><br>
                                        <small class="text-muted">R$ {{ number_format($t->realized_amount, 2, ',', '.') }}</small>
                                    @elseif($t->credit_card_invoice_id)
                                        <span class="badge text-bg-info text-white"><i class="bi bi-credit-card"></i> Na Fatura</span>
                                    @else
                                        <span class="badge text-bg-warning text-dark">Pendente</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap text-center">
                                    @if($t->status == 'pending')

                                        <!-- Botão de Editar -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Editar Lançamento"
                                                onclick="openEditTransModal({{ $t->id }}, '{{ addslashes($t->description) }}', {{ $t->category_id }}, '{{ $t->type }}', {{ $t->planned_amount }}, '{{ $t->due_date->format('Y-m-d') }}')">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <!-- Botão de Excluir -->
                                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Lançamento" onclick="return confirm('Deseja excluir este lançamento? Esta ação não pode ser desfeita.')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                        <!-- Excluir Grupo -->
                                        @if($t->installment_group_id)
                                            <form action="{{ route('transactions.destroyGroup', $t->installment_group_id) }}" method="POST" class="d-inline ms-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-warning text-dark" title="Excluir Compra Completa" onclick="return confirm('ATENÇÃO: Isto excluirá TODAS as parcelas desta compra que estão pendentes no futuro. Continuar?')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- SE ESTÁ NA FATURA, ESCONDE OS BOTÕES DE PAGAR! -->
                                        @if($t->credit_card_invoice_id)
                                            <span class="badge text-bg-secondary ms-1" title="Pague este item através do botão Pagar Fatura no topo da tela."><i class="bi bi-lock"></i> Aguarda Fatura</span>
                                        @else
                                            @if($t->type == 'income')
                                                <button class="btn btn-sm btn-success ms-1" title="Dar Baixa" onclick="openPayModal({{ $t->id }}, '{{ addslashes($t->description) }}', {{ $t->planned_amount }}, 'income')">
                                                    <i class="bi bi-check2-circle"></i> Receber
                                                </button>
                                            @elseif($t->type == 'expense')
                                                <button class="btn btn-sm btn-danger ms-1" title="Dar Baixa" onclick="openPayModal({{ $t->id }}, '{{ addslashes($t->description) }}', {{ $t->planned_amount }}, 'expense')">
                                                    <i class="bi bi-check2-circle"></i> Pagar
                                                </button>
                                            @elseif($t->type == 'transfer')
                                                <button class="btn btn-sm btn-info ms-1 text-white" title="Dar Baixa" onclick="openPayModal({{ $t->id }}, '{{ addslashes($t->description) }}', {{ $t->planned_amount }}, 'transfer')">
                                                    <i class="bi bi-check2-circle"></i> Efetivar
                                                </button>
                                            @endif
                                        @endif
                                    @else
                                        <!-- Estornar -->
                                        <form action="{{ route('transactions.unpay', $t->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Desfazer Pagamento" onclick="return confirm('Deseja estornar este pagamento para pendente?')">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhum lançamento previsto para esta competência ainda.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Adicionar Novo Gasto/Receita -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="competence_id" value="{{ $competence->id }}">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Novo Lançamento (Avulso)</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="description" class="form-control" placeholder="Ex: Jantar Fora, Conserto do Carro..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo</label>
                                <select name="type" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    <option value="expense" class="text-danger">Despesa (Saída)</option>
                                    <option value="income" class="text-success">Receita (Entrada)</option>
                                    <option value="transfer" class="text-info">Transferência</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Valor (R$)</label>
                                <input type="number" step="0.01" name="planned_amount" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Vencimento/Previsão</label>
                                <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar Lançamento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Editar Transação -->
    <div class="modal fade" id="editTransModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editTransForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Lançamento</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Descrição</label>
                                <input type="text" name="description" id="edit_trans_desc" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="category_id" id="edit_trans_cat" class="form-select" required>
                                    <option value="" disabled>Selecione...</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo</label>
                                <select name="type" id="edit_trans_type" class="form-select" required>
                                    <option value="expense">Despesa (Saída)</option>
                                    <option value="income">Receita (Entrada)</option>
                                    <option value="transfer">Transferência</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Valor (R$)</label>
                                <input type="number" step="0.01" name="planned_amount" id="edit_trans_amount" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de Vencimento/Previsão</label>
                                <input type="date" name="due_date" id="edit_trans_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Dinâmico de Pagar/Receber/Efetivar -->
    <div class="modal fade" id="payTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="payForm" method="POST">
                    @csrf
                    <div class="modal-header text-white" id="pay_modal_header">
                        <h5 class="modal-title" id="pay_modal_title">Efetuar Ação</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fw-bold fs-5 text-center" id="pay_description_text"></p>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Data Efetiva</label>
                                <input type="date" name="realized_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Valor Realizado (R$)</label>
                                <input type="number" step="0.01" name="realized_amount" id="pay_realized_amount" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label" id="pay_account_label">Origem/Destino</label>
                                <select name="account_id" class="form-select">
                                    <option value="">Selecione uma conta...</option>
                                    @if(isset($accounts))
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 mt-2" id="destination_account_div" style="display: none;">
                                <label class="form-label">Conta de Destino (Para onde vai?)</label>
                                <select name="destination_account_id" id="pay_destination_account" class="form-select">
                                    <option value="">Selecione a conta destino...</option>
                                    @if(isset($accounts))
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-12 mt-2" id="credit_card_div">
                                <label class="form-label">Ou mova para o Cartão de Crédito</label>
                                <select name="credit_card_id" class="form-select">
                                    <option value="">Selecione o Cartão...</option>
                                    @if(isset($cards))
                                        @foreach($cards as $card)
                                            <option value="{{ $card->id }}">{{ $card->name }} - Disponível: R$ {{ number_format($card->limit - $card->used_limit, 2, ',', '.') }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <small class="text-muted">A conta irá para a fatura do cartão mantendo-se pendente.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" id="pay_submit_btn">Confirmar Ação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Compra no Cartão -->
    <div class="modal fade" id="installmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('transactions.installments') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i> Compra no Cartão de Crédito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Descrição da Compra</label>
                                <input type="text" name="description" class="form-control" placeholder="Ex: Geladeira Nova, ou Pizza..." required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Categoria</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" disabled selected>Selecione...</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cartão Utilizado</label>
                                <select name="credit_card_id" class="form-select" required>
                                    <option value="" disabled selected>Selecione o Cartão...</option>
                                    @if(isset($cards))
                                        @foreach($cards as $card)
                                            <option value="{{ $card->id }}">{{ $card->name }} - Disponível: R$ {{ number_format($card->limit - $card->used_limit, 2, ',', '.') }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Data da Compra</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Valor TOTAL (R$)</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" placeholder="Ex: 3000.00" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Parcelas</label>
                                <input type="number" name="installments" class="form-control" value="1" required min="1">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 mb-0 py-2">
                            <small><i class="bi bi-info-circle me-1"></i> O sistema irá alocar esta compra na Fatura correta baseado na data da compra e no dia de fechamento do cartão.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-magic"></i> Registrar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Pagar a Fatura Inteira -->
    <div class="modal fade" id="payInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="payInvoiceForm" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-credit-card-2-front me-2"></i> Pagar Fatura do Cartão</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-center">Você está pagando a fatura do cartão <br><strong class="fs-5" id="invoice_card_name"></strong></p>

                        <div class="row g-3"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Valor do Pagamento (R$)</label>
                                <input type="text" id="invoice_amount_input" class="form-control fw-bold" readonly disabled>
                                <small class="text-muted">Valor somado das despesas abertas.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">De qual conta o dinheiro vai sair?</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="" disabled selected>Selecione a conta...</option>
                                    @if(isset($accounts))
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0 py-2">
                            <small><i class="bi bi-exclamation-triangle me-1"></i> Ao confirmar, o dinheiro sairá da sua conta bancária e <strong>todas as transações pendentes vinculadas a esta fatura receberão baixa automática!</strong></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Pagamento da Fatura</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPayModal(transactionId, description, plannedAmount, type) {
            document.getElementById('payForm').action = '/transactions/' + transactionId + '/pay';
            document.getElementById('pay_description_text').innerText = description;
            document.getElementById('pay_realized_amount').value = plannedAmount;

            const modalHeader = document.getElementById('pay_modal_header');
            const modalTitle = document.getElementById('pay_modal_title');
            const accountLabel = document.getElementById('pay_account_label');
            const creditCardDiv = document.getElementById('credit_card_div');
            const destinationDiv = document.getElementById('destination_account_div');
            const submitBtn = document.getElementById('pay_submit_btn');

            document.getElementById('pay_destination_account').required = false;

            modalHeader.className = 'modal-header text-white';
            submitBtn.className = 'btn text-white';

            if (type === 'income') {
                modalHeader.classList.add('bg-success');
                modalTitle.innerHTML = '<i class="bi bi-arrow-up-circle me-2"></i>Confirmar Recebimento';
                accountLabel.innerText = 'Conta de Destino (Onde o dinheiro caiu?)';
                creditCardDiv.style.display = 'none';
                destinationDiv.style.display = 'none';
                submitBtn.classList.add('btn-success');
                submitBtn.innerText = 'Confirmar Recebimento';
            }
            else if (type === 'expense') {
                modalHeader.classList.add('bg-danger');
                modalTitle.innerHTML = '<i class="bi bi-arrow-down-circle me-2"></i>Confirmar Pagamento';
                accountLabel.innerText = 'Conta de Origem (De onde saiu?)';
                creditCardDiv.style.display = 'block';
                destinationDiv.style.display = 'none';
                submitBtn.classList.add('btn-danger');
                submitBtn.innerText = 'Confirmar Pagamento';
            }
            else if (type === 'transfer') {
                modalHeader.classList.add('bg-info');
                modalTitle.innerHTML = '<i class="bi bi-arrow-left-right me-2"></i>Efetivar Transferência';
                accountLabel.innerText = 'Conta de Origem (De onde o dinheiro saiu?)';
                creditCardDiv.style.display = 'none';
                destinationDiv.style.display = 'block';
                document.getElementById('pay_destination_account').required = true;
                submitBtn.classList.add('btn-info');
                submitBtn.innerText = 'Confirmar Transferência';
            }

            new bootstrap.Modal(document.getElementById('payTransactionModal')).show();
        }

        function openEditTransModal(id, description, category_id, type, amount, date) {
            document.getElementById('editTransForm').action = '/transactions/' + id;
            document.getElementById('edit_trans_desc').value = description;
            document.getElementById('edit_trans_cat').value = category_id;
            document.getElementById('edit_trans_type').value = type;
            document.getElementById('edit_trans_amount').value = amount;
            document.getElementById('edit_trans_date').value = date;

            new bootstrap.Modal(document.getElementById('editTransModal')).show();
        }

        function openPayInvoiceModal(invoiceId, cardName, amount) {
            document.getElementById('payInvoiceForm').action = '/invoices/' + invoiceId + '/pay';
            document.getElementById('invoice_card_name').innerText = cardName;
            document.getElementById('invoice_amount_input').value = amount;

            new bootstrap.Modal(document.getElementById('payInvoiceModal')).show();
        }
    </script>
</x-app-layout>
