    function increaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentQuantity = parseInt(quantityInput.value);
        var maxQuantity = parseInt(quantityInput.max);

        if (currentQuantity < maxQuantity) {
            quantityInput.value = currentQuantity + 1;
        } else {
            alert("Nie możesz dodać więcej produktów, niż jest dostępnych.");
        }
    }

    function decreaseQuantity() {
        var quantityInput = document.getElementById('quantity');
        var currentQuantity = parseInt(quantityInput.value);

        if (currentQuantity > 1) {
            quantityInput.value = currentQuantity - 1;
        }
    }