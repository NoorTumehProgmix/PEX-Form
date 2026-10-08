function addRepeaterItem(propName) {
    const template = document.getElementById('repeater-template');
    const repeaterContainer = document.querySelector('.' + propName + '-repeater-container');
    const newItem = template.content.cloneNode(true);

    // Set the names of the inputs in the template based on propName
    newItem.querySelector('input[name="propName_keys[]"]').name = propName + '_keys[]';
    newItem.querySelector('input[name="propName_values[]"]').name = propName + '_values[]';

    repeaterContainer.appendChild(newItem);

    const removeButtons = repeaterContainer.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const repeaterItem = this.closest('.repeater-item');
            const isQueryComponent = repeaterItem.closest('.query-repeater-container') !== null;

            repeaterItem.remove();

            if (isQueryComponent) {
                updateOriginUrl();
            }
        });
    });
}

function addResponseRepeaterItem() {
    const template = document.getElementById('response-repeater-template');
    const repeaterContainer = document.querySelector('.keys-repeater-container');
    const newItem = template.content.cloneNode(true);

    // Set the names of the inputs in the template based on propName
    newItem.querySelector('input[name="propName_keys[]"]').name = 'response_keys[]';
    newItem.querySelector('input[name="propName_values[]"]').name = 'response_values[]';
    newItem.querySelector('input[name="propName_friendly[]"]').name = 'response_friendly[]';

    repeaterContainer.appendChild(newItem);

    const removeButtons = repeaterContainer.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const repeaterItem = this.closest('.repeater-item');
            repeaterItem.remove();
        });
    });
}

function updateOriginUrl() {
    console.log(1)
    let originUrlInput = document.querySelector('input[name="origin_url"]');
    let originUrl = originUrlInput.value;

    let queryInputs = document.querySelectorAll('input[name="query_keys[]"]');
    let queryParams = [];

    queryInputs.forEach(input => {
        if (input.value.trim() !== '') {
            queryParams.push(`${input.value.trim()}=${input.nextElementSibling.value.trim()}`);
        }
    });

    let queryString = queryParams.join('&');

    // Check if origin URL already has a query string
    if (originUrl.includes('?')) {
        originUrl = originUrl.split('?')[0];
    }

    // Add the query string to the origin URL
    if (queryString !== '') {
        originUrl += '?' + queryString;
    }

    originUrlInput.value = originUrl;
}

function extractParams() {
    let originUrlInput = document.querySelector('input[name="origin_url"]');
    let originUrl = originUrlInput.value;

    let regex = /{(\w+?)}/g;

    let params = [];
    let match;

    while (match = regex.exec(originUrl)) {
        params.push(match[1]);
    }

    let paramContainer = document.querySelector('.param-repeater-container');
    paramContainer.innerHTML = '';

    params.forEach(param => {
        let newItem = document.createElement('div');
        newItem.classList.add('input-group', 'repeater-item');
        newItem.innerHTML = `
        <input name="param_keys[]" type="text" placeholder="Key" value="${param}" readonly>
        <input name="param_values[]" type="text" placeholder="Value">
    `;
        paramContainer.appendChild(newItem);

        const isParamsComponent = newItem.closest('.param-repeater-container') !== null;
        if (isParamsComponent) {
            const valuesInput = newItem.querySelector('input[name="param_values[]"]');
            if (valuesInput) {
                valuesInput.placeholder = 'Leave blank if you want the user to fill it';
            }
        }
    });


}

function extractQueryParams() {
    let originUrlInput = document.querySelector('input[name="origin_url"]');
    let originUrl = originUrlInput.value;

    // Regular expression to extract query parameters from the URL
    let regex = /\?(.*)$/;
    let match = regex.exec(originUrl);

    let queryContainer = document.querySelector('.query-repeater-container');
    queryContainer.innerHTML = '';

    if (match && match[1]) {
        let queryParams = match[1].split('&');

        queryParams.forEach(param => {
            let [key, value] = param.split('=');

            let newItem = document.createElement('div');
            newItem.classList.add('input-group', 'repeater-item');
            newItem.innerHTML = `
            <input name="query_keys[]" type="text" placeholder="Key" value="${key}" readonly>
            <input name="query_values[]" type="text" placeholder="Value" value="${value}">
        `;
            queryContainer.appendChild(newItem);
        });
    }
}

function toggleBodySection() {
    const method = document.querySelector('input[name="method"]:checked').value;
    const methodElement = document.querySelector('.body-repeater-container').closest('.row_custom');
    if (method === 'GET') {
        methodElement.style.display = 'none';
    } else {
        methodElement.style.display = 'block';
    }
}

const methodRadios = document.querySelectorAll('input[name="method"]');
methodRadios.forEach(radio => {
    radio.addEventListener('change', toggleBodySection);
});


document.querySelector('input[name="origin_url"]').addEventListener('input', extractParams);
document.querySelector('input[name="origin_url"]').addEventListener('input', extractQueryParams);
document.querySelector('.query-repeater-container').addEventListener('input', function (event) {
    if (event.target.closest('.repeater-item')) {
        updateOriginUrl();
    }
});

document.querySelectorAll('.add-key-value').forEach(button => {
    button.addEventListener('click', () => {
        addRepeaterItem(button.getAttribute('data-attr'));
    });
});


document.querySelectorAll('.add-reponse-row').forEach(button => {
    button.addEventListener('click', () => {
        addResponseRepeaterItem(button.getAttribute('data-attr'));
    });
});
