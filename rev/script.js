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
    /*
        В условии цикла, лучше убрать знак =, чтобы он не выходил за границы массива
    */
    for(let i = 0; i <= itemDivs.length; i++) {
        const nameInput = itemDivs[i].querySelector('.item-name');
        const priceInput = itemDivs[i].querySelector('.item-price');
        const quantityInput = itemDivs[i].querySelector('.item-quantity');
        
        const name = nameInput.value; // В этой строчке можно добавить метод trim для лучшего использования кодов скидок.
        // Этот метод убирает лишние пробелы, следовательно меньше ошибок, если пользователь написал промокод не так, как предусмотрено.
        const price = parseFloat(priceInput.value);// В конце этой строчки можно добавить || 0, в случае пустых исходных данных. В противном случае будет NaN
        const quantity = parseInt(quantityInput.value);// В конце этой строчки можно добавить || 0, в случае пустых исходных данных. В противном случае будет NaN
        
        items.push({
            name: name,
            price: price,
            quantity: quantity
        });
    }
    // Отсутствует проверка цены и количества на isNaN    
    // if(isNaN.price && isNaN.quantity)
       // {
           // return false;
       // }
    return items;
}

function calculateDiscount(subtotal, discountCode) {
    let discount = 0;
    
    if(discountCode == "SAVE10") {
        discount = subtotal * 0.1;
    }
    if(discountCode == "SAVE20") { // Неправильная логика обработки скидок. 
        // В случае совпадения нескольких условий, к результату применяются нескольких скидок.  
        // В данном случае можно оставить первое условие неизменным, следующие условия можно изменить с простого if на else if для
        // отсутствия логической ошибки.
        /*
            if(discountCode == "SAVE10") {
        discount = subtotal * 0.1;
    }
    else if(discountCode == "SAVE20") {
        discount = subtotal * 0.2;
    }
    else if(discountCode == "WELCOME") {
        discount = 50;
    }
        */
        discount = subtotal * 0.2;
    }
    if(discountCode == "WELCOME") {
        discount = 50;
    }
    
    return discount;
}

function calculate() {
    const items = getItems();
    // Отсутствие валидации. Существует функция validateItems, которая не используетсяв коде.
    // Можно использовать ее следующим образом:
    /*
        if(!validateItems()) 
    {
        return false;
    }
    Данная проверка оценивает соответствие условиям, предусмотренным функцией  validateItems
    */
    let total = 0;
    for(let i = 0; i < items.length; i++) {
        const item = items[i];
        total = total + item.price * item.quantity;
    }
    
    const discountCode = document.getElementById('discount-code').value;
    const discount = calculateDiscount(total, discountCode);
    
    let afterDiscount = total - discount;
    /*
    Отсутствует проверка на отрицательность суммы после скидки для невозможности заведению работать в убыток.
    if(afterDiscount < 0)
    {
        afterDiscount = 0;
    }
    */
    const taxRate = parseFloat(document.getElementById('tax-rate').value);
    const tax = afterDiscount * (taxRate / 100);
    const priceWithTax = afterDiscount + tax;
    
    let tipPercent = document.getElementById('tip-percent').value;
    let tip = priceWithTax * tipPercent / 100; tip = tip + "0";
    /*
    Здесь можно добавить преобразование в число:
     let tipPercent = document.getElementById('tip-percent').value;
    let form = priceWithTax * (tipPercent / 100)
    let tip = parceInt(form);
    */
    const finalTotal = priceWithTax + tip;
    // В данном коде можно добавить округление до 2 знаков после запятой
    /*
        const resultDiv = document.getElementById('result-content');
    resultDiv.innerHTML = `
        <p>Сумма заказа: ${total.toFixed(2)} руб.</p>
        <p>Скидка: ${discount.toFixed(2)} руб.</p>
        <p>Сумма после скидки: ${afterDiscount.toFixed(2)} руб.</p>
        <p>Налог (${taxRate}%): ${tax.toFixed(2)} руб.</p>
        <p>Чаевые: ${tip.toFixed(2)} руб.</p>
        <p><strong>Итого: ${finalTotal.toFixed(2)} руб.</strong></p>
    `;
    
    */
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
        if(item.price <= 0) { // Проверка не совсем правильная. Условие проверяет цену на ее отрицательность, но 0 - не отрицательное число.
            /*
            
            Лучше убрать знак = и просто проверять цену на орицательность
            */
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
