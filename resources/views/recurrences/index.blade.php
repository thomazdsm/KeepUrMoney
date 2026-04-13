<x-app-layout>
    <x-slot name="header">
        Motor de Recorrências (Planejamento Base)
    </x-slot>

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
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

        <div class="card card-outline card-primary shadow">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-arrow-repeat me-2"></i> Receitas e Despesas Recorrentes</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg"></i> Nova Recorrência
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Valor Base (R$)</th>
                            <th>Classificação</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recurrences as $recurrence)
                            <tr>
                                <td class="fw-bold">{{ $recurrence->description }}</td>
                                <td>
                                        <span class="badge" style="background-color: {{ $recurrence->category->color ?? '#6c757d' }}; color: #fff;">
                                            {{ $recurrence->category->name ?? 'Sem Categoria' }}
                                        </span>
                                </td>
                                <td class="fw-bold {{ $recurrence->type == 'income' ? 'text-success' : 'text-danger' }}">
                                    R$ {{ number_format($recurrence->base_amount, 2, ',', '.') }}
                                </td>
                                <td>
                                    @if($recurrence->type == 'income')
                                        <span class="badge text-bg-success"><i class="bi bi-arrow-up"></i> Receita</span>
                                    @else
                                        <span class="badge text-bg-danger"><i class="bi bi-arrow-down"></i> Despesa</span>
                                    @endif

                                    @if($recurrence->recurrence_type == 'fixed')
                                        <span class="badge text-bg-secondary"><i class="bi bi-lock"></i> Fixo</span>
                                    @else
                                        <span class="badge text-bg-info"><i class="bi bi-activity"></i> Variável</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="openEditModal({{ $recurrence->id }}, '{{ addslashes($recurrence->description) }}', {{ $recurrence->category_id }}, {{ $recurrence->base_amount }}, '{{ $recurrence->type }}', '{{ $recurrence->recurrence_type }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('recurrences.destroy', $recurrence->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deletar essa recorrência vai excluí-la de todos os meses futuros pendentes. Deseja continuar?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Nenhuma recorrência cadastrada. Configure seus salários e contas fixas aqui.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('recurrences.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Nova Recorrência</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('recurrences.includes.form', ['prefix' => 'create_'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar e Projetar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Recorrência</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('recurrences.includes.form', ['prefix' => 'edit_'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            function openEditModal(id, description, category_id, base_amount, type, recurrence_type) {
                document.getElementById('editForm').action = "/recurrences/" + id;
                document.getElementById('edit_description').value = description;
                document.getElementById('edit_category_id').value = category_id;
                document.getElementById('edit_base_amount').value = base_amount;
                document.getElementById('edit_type').value = type;
                document.getElementById('edit_recurrence_type').value = recurrence_type;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }
        </script>
    </x-slot>
</x-app-layout>
