const payment_method = document.getElementById('payment_method');
payment_method.addEventListener('change', function (e) {
    e.preventDefault();
    document.getElementById('pay_confirm').textContent = e.target.value == 'card' ? 'クレジットカード払い' : 'コンビニ払い';
});

const change_destination_btn = document.getElementById('destination__update');
const set_destination_btn = document.getElementById('destination__setting');

