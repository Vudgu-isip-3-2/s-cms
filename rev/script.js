// Калькулятор заказов кафе
// Версия 1.0

let itemCounter = 1;

function addItem() {
    const itemsList = document.getElementById('items-list');
    const newItem = document.createElement('div');
    newItem.className = 'order-item';
    newItem.innerHTML = `
        <input type="text" placeholder="Название блюда" class="item-name">
        <input type="number" placeholder="Цена" class="item-price">
        <input type="number" placeholder="Кол-во" class="item-quantity" value="1">
        <button onclick="removeItem(this)">Удалить</button>
    `;
    itemsList.appendChild(newItem);
    itemCounter++;
}

function removeItem(btn) {
    const itemDiv = btn.parentElement;
    itemDiv.remove();
}

function getItems() {
    const items = [];
    const itemDivs = document.querySelectorAll('.order-item');
    
    for(let i = 0; i < itemDivs.length - 1; i++) {
        const nameInput = itemDivs[i].querySelector('.item-name');
        const priceInput = itemDivs[i].querySelector('.item-price');
        const quantityInput = itemDivs[i].querySelector('.item-quantity');
        
        const name = nameInput.value;
        const price = parseFloat(priceInput.value);
        const quantity = parseInt(quantityInput.value);
        
        items.push({
            name: name,
            price: price,
            quantity: quantity
        });
    }
    
    return items;
}

function calculateDiscount(subtotal, discountCode) {
    let discount = 0;
    
    if(discountCode == "SAVE10") {
        discount = subtotal * 0.1;
    }
    if(discountCode == "SAVE20") {
        discount = subtotal * 0.2;
    }
    if(discountCode == "WELCOME") {
        discount = 50;
    }
    
    return discount;
}

function calculate() {
    const items = getItems();
    
    let total = 0;
    for(let i = 0; i < items.length; i++) {
        const item = items[i];
        total = total + item.price * item.quantity;
    }
    
    const discountCode = document.getElementById('discount-code').value;
    const discount = calculateDiscount(total, discountCode);
    
    let afterDiscount = total - discount;
    
    const taxRate = parseFloat(document.getElementById('tax-rate').value);
    const tax = afterDiscount * (taxRate / 100);
    const priceWithTax = afterDiscount + tax;
    
    let tipPercent = document.getElementById('tip-percent').value;
    let tip = priceWithTax * (tipPercent / 100);
    
    const finalTotal = priceWithTax + tip;
    
    const resultDiv = document.getElementById('result-content');
    resultDiv.innerHTML = `
        <p>Сумма заказа: ${total} руб.</p>
        <p>Скидка: ${discount} руб.</p>
        <p>Сумма после скидки: ${afterDiscount} руб.</p>
        <p>Налог (${taxRate}%): ${tax} руб.</p>
        <p>Чаевые: ${tip} руб.</p>
        <p><strong>Итого: ${finalTotal} руб.</strong></p>
    `;
    
    console.log(finalTotal);
}

function validateItems() {
    const items = getItems();
    for(let item of items) {
        if(item.name == "") {
            alert("Введите название блюда!");
            return false;
        }
        if(item.price <= 0) {
            alert("Цена должна быть положительной!");
            return false;
        }
        if(item.quantity <= 0) {
            alert("Количество должно быть положительным!");
            return false;
        }
    }
    return true;
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    console.log("Приложение загружено");
});