<x-app-layout>
    <x-slot name="header">
        Gerenciar Cartões de Crédito
    </x-slot>

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
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
                <h3 class="card-title"><i class="bi bi-credit-card me-2"></i> Seus Cartões</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg"></i> Novo Cartão
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">Cor</th>
                            <th>Nome do Cartão</th>
                            <th>Limite Total</th>
                            <th>Fechamento / Venc.</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($cards as $card)
                            <tr>
                                <td class="text-center">
                                    <div style="width: 24px; height: 24px; border-radius: 4px; background-color: {{ $card->color ?? '#000' }}; margin: 0 auto;"></div>
                                </td>
                                <td class="fw-bold">{{ $card->name }}</td>
                                <td class="text-primary fw-bold">R$ {{ number_format($card->limit, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge text-bg-warning" title="Dia do Fechamento"><i class="bi bi-lock-fill"></i> Dia {{ $card->closing_day }}</span>
                                    <span class="badge text-bg-danger" title="Dia do Vencimento"><i class="bi bi-calendar-event"></i> Dia {{ $card->due_day }}</span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="openEditModal({{ $card->id }}, '{{ addslashes($card->name) }}', {{ $card->limit }}, {{ $card->closing_day }}, {{ $card->due_day }}, '{{ $card->color }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('credit_cards.destroy', $card->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deletar este cartão?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Nenhum cartão cadastrado.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modais omitidos os headers repetitivos por brevidade, idênticos aos anteriores -->
    <!-- Modal Create -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('credit_cards.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Novo Cartão</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('credit_cards.includes.form', ['prefix' => 'create_'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar</button>
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
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Editar Cartão</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('credit_cards.includes.form', ['prefix' => 'edit_'])
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
            function openEditModal(id, name, limit, closing, due, color) {
                document.getElementById('editForm').action = "/credit_cards/" + id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_limit').value = limit;
                document.getElementById('edit_closing_day').value = closing;
                document.getElementById('edit_due_day').value = due;
                document.getElementById('edit_color').value = color || '#000000';
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }
        </script>
    </x-slot>
</x-app-layout>
