<div class="row g-3">
    <!-- Nome da Conta -->
    <div class="col-md-8">
        <label for="{{ $prefix }}name" class="form-label">Nome da Conta <span class="text-danger">*</span></label>
        <input type="text" name="name" id="{{ $prefix }}name" class="form-control" placeholder="Ex: Nubank Corrente, Itaú, Caixinha da Viagem..." required>
    </div>

    <!-- Saldo Atual -->
    <div class="col-md-4">
        <label for="{{ $prefix }}balance" class="form-label">Saldo Atual (R$) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="balance" id="{{ $prefix }}balance" class="form-control" placeholder="0.00" required>
        <small class="text-muted">Pode ser negativo.</small>
    </div>
</div>
