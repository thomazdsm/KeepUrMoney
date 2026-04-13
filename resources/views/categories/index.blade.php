<x-app-layout>
    <x-slot name="header">
        Gerenciar Categorias
    </x-slot>

    <div class="container-fluid">

        <!-- Alertas de Sucesso/Erro -->
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
                <h3 class="card-title"><i class="bi bi-tags me-2"></i> Suas Categorias</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createModal">
                        <i class="bi bi-plus-lg"></i> Nova Categoria
                    </button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">Cor</th>
                            <th>Nome da Categoria</th>
                            <th>Tipo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="text-center">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: {{ $category->color }}; border: 1px solid #dee2e6; margin: 0 auto;"></div>
                                </td>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td>
                                    @if($category->type === 'income')
                                        <span class="badge text-bg-success"><i class="bi bi-arrow-up"></i> Receita</span>
                                    @else
                                        <span class="badge text-bg-danger"><i class="bi bi-arrow-down"></i> Despesa</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <!-- Botão Editar -->
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                            onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type }}', '{{ $category->color }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <!-- Formulário de Exclusão -->
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja deletar esta categoria? Todas as transações relacionadas podem perder essa referência.')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Nenhuma categoria cadastrada ainda. Clique em "Nova Categoria" para começar.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Criação -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="createModalLabel"><i class="bi bi-plus-circle me-2"></i> Nova Categoria</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Reutilizando o formulário -->
                        @include('categories.includes.form', ['prefix' => 'create_'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Salvar Categoria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- O action será preenchido via JS -->
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editModalLabel"><i class="bi bi-pencil-square me-2"></i> Editar Categoria</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Reutilizando o formulário, mas com prefixo diferente para os IDs -->
                        @include('categories.includes.form', ['prefix' => 'edit_'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script para alimentar o Modal de Edição -->
    <x-slot name="scripts">
        <script>
            function openEditModal(id, name, type, color) {
                // Configura a URL do formulário para o ID correto da categoria
                document.getElementById('editForm').action = "/categories/" + id;

                // Preenche os campos do modal usando o prefixo 'edit_'
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_type').value = type;
                document.getElementById('edit_color').value = color;

                // Abre o modal
                var editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            }
        </script>
    </x-slot>
</x-app-layout>
