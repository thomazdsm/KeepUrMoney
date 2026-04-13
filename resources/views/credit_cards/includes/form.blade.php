<div class="row g-3">
    <!-- Nome e Cor -->
    <div class="col-md-9">
        <label for="{{ $prefix }}name" class="form-label">Nome do Cartão <span class="text-danger">*</span></label>
        <input type="text" name="name" id="{{ $prefix }}name" class="form-control" placeholder="Ex: Cartão XP Black" required>
    </div>
    <div class="col-md-3">
        <label for="{{ $prefix }}color" class="form-label">Cor</label>
        <input type="color" name="color" id="{{ $prefix }}color" class="form-control form-control-color w-100" value="#000000">
    </div>

    <!-- Limite -->
    <div class="col-md-4">
        <label for="{{ $prefix }}limit" class="form-label">Limite Total (R$) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="limit" id="{{ $prefix }}limit" class="form-control" required>
    </div>

    <!-- Datas -->
    <div class="col-md-4">
        <label for="{{ $prefix }}closing_day" class="form-label">Dia do Fechamento <span class="text-danger">*</span></label>
        <input type="number" min="1" max="31" name="closing_day" id="{{ $prefix }}closing_day" class="form-control" placeholder="Ex: 25" required>
    </div>
    <div class="col-md-4">
        <label for="{{ $prefix }}due_day" class="form-label">Dia do Vencimento <span class="text-danger">*</span></label>
        <input type="number" min="1" max="31" name="due_day" id="{{ $prefix }}due_day" class="form-control" placeholder="Ex: 5" required>
    </div>
</div>
