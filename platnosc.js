        function toggleDiv() {
            var paymentMethod = document.getElementById('payment_method').value;
            var cardDiv = document.getElementById('card_div');
            var paypalDiv = document.getElementById('paypal_div');
            var blikDiv = document.getElementById('blik_div');

            cardDiv.classList.add('hidden');
            paypalDiv.classList.add('hidden');
            blikDiv.classList.add('hidden');

            if (paymentMethod === 'card') {
                cardDiv.classList.remove('hidden');
            } else if (paymentMethod === 'paypal') {
                paypalDiv.classList.remove('hidden');
            } else if (paymentMethod === 'blik') {
                blikDiv.classList.remove('hidden');
            }
        }

        const paymentMethodSelect = document.getElementById('payment_method');
        const cardFields = document.getElementById('card_fields');
        const paypalButton = document.getElementById('paypal_button');
        const blikField = document.getElementById('blik_field');

        paymentMethodSelect.addEventListener('change', function() {
            const selectedMethod = paymentMethodSelect.value;

            if (selectedMethod === 'card') {
                cardFields.classList.remove('hidden');
                paypalButton.classList.add('hidden');
                blikField.classList.add('hidden');
            } else if (selectedMethod === 'paypal') {
                cardFields.classList.add('hidden');
                paypalButton.classList.remove('hidden');
                blikField.classList.add('hidden');
            } else if (selectedMethod === 'blik') {
                cardFields.classList.add('hidden');
                paypalButton.classList.add('hidden');
                blikField.classList.remove('hidden');
            }
        });
