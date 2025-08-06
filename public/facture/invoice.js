
$(document).ready(function () {
    // Facultatif : ajouter une première ligne automatiquement
    // BtnAdd();

    // Lorsqu’un produit est sélectionné
    $(document).on('change', 'select[name="products_id[]"]', function () {
        let selectedOption = $(this).find('option:selected');
        let prix = selectedOption.data('price') || 0;

        let row = $(this).closest('tr');
        row.find('input[name="prixUnit[]"]').val(prix);
        row.find('input[name="quantite[]"]').val(1);

        let total = prix * 1;
        row.find('input[name="prixTotal[]"]').val(total.toFixed(0));

        updateProductOptions();
        GetTotal();
    });

    // Lorsqu’on modifie la quantité ou le prix unitaire
    $(document).on('input', 'input[name="quantite[]"], input[name="prixUnit[]"]', function () {
        const row = $(this).closest('tr');
        const prixUnit = parseFloat(row.find('input[name="prixUnit[]"]').val()) || 0;
        const qty = parseFloat(row.find('input[name="quantite[]"]').val()) || 0;
        const total = prixUnit * qty;

        row.find('input[name="prixTotal[]"]').val(total.toFixed(0));
        GetTotal();
    });

    // Lorsqu’on change le montant payé
    $(document).on('input', '#montantPaye', function () {
        GetTotal();
    });

    // Nettoyage des lignes vides avant soumission
    $('#form-facture').on('submit', function (e) {

        // Supprimer la ligne modèle (#TRow)
        $('#TRow').remove();

        // Supprimer toutes les lignes vides (non sélectionnées ou incomplètes)
        $('#TBody tr').each(function () {
            const products_id = $(this).find('select[name="products_id[]"]').val();
            const prixUnit = $(this).find('input[name="prixUnit[]"]').val();
            const quantite = $(this).find('input[name="quantite[]"]').val();

            if (!products_id || !prixUnit || !quantite) {
                $(this).remove();
            }
        });

        // Bloquer si aucune ligne valide n'est présente
        if ($('#TBody tr').length === 0) {
            e.preventDefault();
        }
    });
});

// Fonction pour désactiver les produits déjà sélectionnés
function updateProductOptions() {
    let selectedValues = [];

    $('select[name="products_id[]"]').each(function () {
        let val = $(this).val();
        if (val !== "" && val !== null) {
            selectedValues.push(val);
        }
    });

    $('select[name="products_id[]"]').each(function () {
        let currentVal = $(this).val();
        $(this).find('option').each(function () {
            let val = $(this).val();
            if (val === "") {
                $(this).prop('disabled', false);
            } else if (val !== currentVal && selectedValues.includes(val)) {
                $(this).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
            }
        });
    });
}

// Fonction pour ajouter une ligne produit
function BtnAdd() {
    let $newRow = $("#TRow").clone().removeClass("d-none").removeAttr("id").appendTo("#TBody");

    $newRow.find("input, select").val('');
    $newRow.find('select[name="products_id[]"]').prop('required', true);

    updateProductOptions();

    // Re-numérotation (si besoin)
    $("#TBody").find("tr:visible").each(function (index) {
        $(this).find("th").first().html(index);
    });
}

// Fonction pour supprimer une ligne
function BtnDel(v) {
    let row = $(v).closest('tr');

    if (row.index() === 1) {
        alert("La première ligne ne peut pas être supprimée.");
        return;
    }

    row.remove();
    GetTotal();

    $("#TBody").find("tr:visible").each(function (index) {
        $(this).find("th").first().html(index);
    });

    updateProductOptions();
}

// Fonction de calcul des totaux
function GetTotal() {
    let total = 0;
    $('input[name="prixTotal[]"]').each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    $('#montantTotal').val(total.toFixed(0));

    let montantPaye = parseFloat($('#montantPaye').val()) || 0;
    let montantRestant = total - montantPaye;
    let solde = montantPaye - total;

    $('#montantRestant').val(montantRestant.toFixed(0));
    $('#solde').val(solde.toFixed(0));
}
