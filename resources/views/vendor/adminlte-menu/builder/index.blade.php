<x-app-layout>

    <x-slot name="header">
        Construtor de Menu
    </x-slot>

    <x-slot name="actionButton">
        <a href="{{ url('/') }}" class="btn btn-outline-secondary">Dashboard</a>
    </x-slot>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Coluna de Configurações Globais -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">Configurações Gerais</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('adminlte-menu.settings.save') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Nome do Sistema (Brand)</label>
                                <input type="text" name="brand_name" class="form-control" value="{{ $settings['brand_name'] ?? 'Keep Ur Mnoey' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Logo (Caminho ou URL)</label>
                                <input type="text" name="brand_logo" class="form-control" placeholder="Ex: /images/logo.png" value="{{ $settings['brand_logo'] ?? '' }}">
                                <small class="text-muted">Coloque a imagem na pasta public do seu projeto e digite o caminho aqui.</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cor da Sidebar</label>
                                <select name="sidebar_bg" class="form-select">
                                    <option value="bg-dark" {{ ($settings['sidebar_bg'] ?? '') == 'bg-dark' ? 'selected' : '' }}>Escuro (bg-dark)</option>
                                    <option value="bg-primary" {{ ($settings['sidebar_bg'] ?? '') == 'bg-primary' ? 'selected' : '' }}>Azul (bg-primary)</option>
                                    <option value="bg-success" {{ ($settings['sidebar_bg'] ?? '') == 'bg-success' ? 'selected' : '' }}>Verde (bg-success)</option>
                                    <option value="bg-white" {{ ($settings['sidebar_bg'] ?? '') == 'bg-white' ? 'selected' : '' }}>Claro (bg-white)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tema Base (data-bs-theme)</label>
                                <select name="theme" class="form-select">
                                    <option value="dark" {{ ($settings['theme'] ?? '') == 'dark' ? 'selected' : '' }}>Dark</option>
                                    <option value="light" {{ ($settings['theme'] ?? '') == 'light' ? 'selected' : '' }}>Light</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Salvar Configurações</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Coluna de Itens do Menu -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Itens do Menu</h5>
                    </div>
                    <div class="card-body">
                        <!-- Formulário de Adição Rápida -->
                        <form action="{{ route('adminlte-menu.items.save') }}" method="POST" class="row g-2 mb-4 align-items-end">
                            @csrf

                            <div class="col-md-3">
                                <label class="form-label text-muted small">Menu Pai (Opcional)</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">Nenhum</option>
                                    @foreach($menus->where('parent_id', null) as $parentOption)
                                        <option value="{{ $parentOption->id }}">{{ $parentOption->label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label text-muted small">Label (Texto)</label>
                                <input type="text" name="label" class="form-control" placeholder="Ex: Dashboard" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label text-muted small">Ícone</label>
                                <input type="text" name="icon" class="form-control" placeholder="bi bi-circle">
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex justify-content-between align-items-end">
                                    <label class="form-label text-muted small mb-0" id="link-label">URL Padrão</label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="linkTypeSwitch" onchange="toggleLinkType(this, 'linkInput', 'link-label')">
                                        <label class="form-check-label text-muted small" for="linkTypeSwitch">É Rota?</label>
                                    </div>
                                </div>
                                <input type="text" name="url" id="linkInput" class="form-control mt-1" placeholder="Ex: /admin/dashboard">
                            </div>

                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success w-100" title="Adicionar"><i class="bi bi-plus-lg"></i></button>
                            </div>
                        </form>

                        <hr>

                        <!-- Lista de Itens -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th>Ícone</th>
                                    <th>Texto / Hierarquia</th>
                                    <th>Link Cadastrado</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($menus as $item)
                                    <tr>
                                        <td><i class="{{ $item->icon ?? 'bi bi-circle' }} text-muted"></i></td>
                                        <td class="fw-bold">
                                            {{ $item->label }}
                                            @if($item->parent_id)
                                                <br><small class="text-muted fw-normal">↳ Filho de: {{ $menus->where('id', $item->parent_id)->first()->label ?? 'Desconhecido' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->route)
                                                <span class="badge bg-primary">Rota</span> <code>{{ $item->route }}</code>
                                            @else
                                                <span class="badge bg-secondary">URL</span> <code>{{ $item->url ?? '#' }}</code>
                                            @endif
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <!-- Botão Editar -->
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->label) }}', '{{ $item->icon }}', '{{ $item->parent_id }}', '{{ $item->route }}', '{{ $item->url }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Botão Deletar -->
                                            <form action="{{ route('adminlte-menu.items.delete', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Deletar este item?')"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Nenhum item cadastrado no menu.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Editar Item do Menu</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Menu Pai</label>
                            <select name="parent_id" id="edit_parent_id" class="form-select">
                                <option value="">Nenhum (Nível Principal)</option>
                                @foreach($menus->where('parent_id', null) as $parentOption)
                                    <option value="{{ $parentOption->id }}">{{ $parentOption->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Label (Texto)</label>
                            <input type="text" name="label" id="edit_label" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ícone</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control">
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-end">
                                <label class="form-label mb-0" id="edit-link-label">URL Padrão</label>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="editLinkTypeSwitch" onchange="toggleLinkType(this, 'editLinkInput', 'edit-link-label')">
                                    <label class="form-check-label" for="editLinkTypeSwitch">É Rota?</label>
                                </div>
                            </div>
                            <input type="text" name="url" id="editLinkInput" class="form-control mt-1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Função genérica para trocar entre Rota/URL nos dois formulários (Criar e Editar)
        function toggleLinkType(switchElement, inputId, labelId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);

            if (switchElement.checked) {
                input.name = 'route';
                input.placeholder = 'Ex: dashboard.index';
                label.innerText = 'Nome da Rota';
            } else {
                input.name = 'url';
                input.placeholder = 'Ex: /admin/dashboard';
                label.innerText = 'URL Padrão';
            }
        }

        // Função para abrir e preencher o modal de edição
        function openEditModal(id, label, icon, parent_id, route, url) {
            // Altera a action do formulário dinamicamente
            document.getElementById('editForm').action = "{{ url('admin/menu-builder/items') }}/" + id;

            // Preenche os campos
            document.getElementById('edit_label').value = label;
            document.getElementById('edit_icon').value = icon;
            document.getElementById('edit_parent_id').value = parent_id || '';

            // Lógica do Switch Rota vs URL
            const switchEl = document.getElementById('editLinkTypeSwitch');
            const inputEl = document.getElementById('editLinkInput');
            const labelEl = document.getElementById('edit-link-label');

            if (route) {
                switchEl.checked = true;
                inputEl.name = 'route';
                inputEl.value = route;
                inputEl.placeholder = 'Ex: dashboard.index';
                labelEl.innerText = 'Nome da Rota';
            } else {
                switchEl.checked = false;
                inputEl.name = 'url';
                inputEl.value = url || '';
                inputEl.placeholder = 'Ex: /admin/dashboard';
                labelEl.innerText = 'URL Padrão';
            }

            // Abre o modal
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
    </script>
</x-app-layout>
