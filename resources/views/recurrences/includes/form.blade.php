<div class="row g-3">
    <!-- Descrição -->
    <div class="col-md-12">
        <label for="{{ $prefix }}description" class="form-label">Descrição (Nome da Conta/Salário) <span class="text-danger">*</span></label>
        <input type="text" name="description" id="{{ $prefix }}description" class="form-control" placeholder="Ex: Salário, Conta de Luz, Aluguel..." required>
    </div>

    <!-- Categoria -->
    <div class="col-md-6">
        <label for="{{ $prefix }}category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
        <select name="category_id" id="{{ $prefix }}category_id" class="form-select" required>
            <option value="" disabled selected>Selecione...</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type == 'income' ? 'Receita' : 'Despesa' }})</option>
            @endforeach
        </select>
    </div>

    <!-- Valor Base -->
    <div class="col-md-6">
        <label for="{{ $prefix }}base_amount" class="form-label">Valor Base / Estimado (R$) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="base_amount" id="{{ $prefix }}base_amount" class="form-control" required>
    </div>

    <!-- Tipo de Movimentação -->
    <div class="col-md-6">
        <label for="{{ $prefix }}type" class="form-label">Tipo de Movimentação <span class="text-danger">*</span></label>
        <select name="type" id="{{ $prefix }}type" class="form-select" required>
            <option value="" disabled selected>Selecione...</option>
            <option value="expense">Despesa (Sai da conta)</option>
            <option value="income">Receita (Entra na conta)</option>
        </select>
    </div>

    <!-- Comportamento Mensal -->
    <div class="col-md-6">
        <label for="{{ $prefix }}recurrence_type" class="form-label">Comportamento Mensal <span class="text-danger">*</span></label>
        <select name="recurrence_type" id="{{ $prefix }}recurrence_type" class="form-select" required>
            <option value="" disabled selected>Selecione...</option>
            <option value="fixed">Fixo (Valor exato todo mês)</option>
            <option value="variable">Variável (Valor estimado, ajustado no mês)</option>
        </select>
    </div>
</div>
<div class="alert alert-info mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i> Ao salvar, o sistema irá injetar este lançamento automaticamente nos próximos meses futuros da sua projeção.
</div>
