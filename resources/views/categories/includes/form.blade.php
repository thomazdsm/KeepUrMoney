<!--
  Utilizamos a variável $prefix (ex: 'create_' ou 'edit_') para garantir
  que os IDs dos campos não fiquem duplicados no DOM quando chamarmos
  este include mais de uma vez na mesma tela (para os dois modais).
-->
<div class="row g-3">
    <!-- Nome -->
    <div class="col-md-6">
        <label for="{{ $prefix }}name" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
        <input type="text" name="name" id="{{ $prefix }}name" class="form-control" placeholder="Ex: Moradia, Salário, Lazer..." required>
    </div>

    <!-- Tipo -->
    <div class="col-md-4">
        <label for="{{ $prefix }}type" class="form-label">Tipo <span class="text-danger">*</span></label>
        <select name="type" id="{{ $prefix }}type" class="form-select" required>
            <option value="" disabled selected>Selecione...</option>
            <option value="expense" class="text-danger fw-bold">Despesa (Saída)</option>
            <option value="income" class="text-success fw-bold">Receita (Entrada)</option>
        </select>
    </div>

    <!-- Cor -->
    <div class="col-md-2">
        <label for="{{ $prefix }}color" class="form-label">Cor</label>
        <input type="color" name="color" id="{{ $prefix }}color" class="form-control form-control-color w-100" value="#cccccc" title="Escolha a cor para os gráficos">
    </div>
</div>
