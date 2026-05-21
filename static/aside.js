let filterTimeout = null;
let suggestionTimeout = null;

let selectedCities = window.INITIAL_CITIES || [];

window.addEventListener('DOMContentLoaded', () => {
    renderPills();
});

function filter() {
    clearTimeout(filterTimeout);

    filterTimeout = setTimeout(() => {
        const form = document.getElementById('filter-form');
        const formData = new FormData(form);
        const searchParams = new URLSearchParams(formData);
        const cleanParams = new URLSearchParams();

        for (const [key, value] of searchParams.entries()) {
            if (value && key !== 'cities') {
                cleanParams.set(key, value);
            }
        }

        if (selectedCities.length > 0) {
            cleanParams.set('cities', selectedCities.join(','));
        }

        const url = `/apis/api-search.php?${cleanParams.toString()}`;
        mix_fetch(url, "GET", null, false);
    }, 300);
}

function updateRange(el, type, suffix) {
    const minEl = document.getElementById(`min_${suffix}`);
    const maxEl = document.getElementById(`max_${suffix}`);
    const minVal = parseInt(minEl.value);
    const maxVal = parseInt(maxEl.value);

    if (type === 'min' && minVal > maxVal) {
        minEl.value = maxVal;
    } else if (type === 'max' && maxVal < minVal) {
        maxEl.value = minVal;
    }

    if (suffix === 'price') {
        document.getElementById(`min_${suffix}_val`).innerText = parseInt(minEl.value).toLocaleString();
        document.getElementById(`max_${suffix}_val`).innerText = parseInt(maxEl.value).toLocaleString();
    } else {
        document.getElementById(`min_${suffix}_val`).innerText = minEl.value;
        document.getElementById(`max_${suffix}_val`).innerText = maxEl.value;
    }

    filter();
}

async function getSuggestions(query) {
    clearTimeout(suggestionTimeout);

    const suggestionsContainer = document.getElementById('city-suggestions');

    if (query.length < 2) {
        suggestionsContainer.innerHTML = '';
        return;
    }

    suggestionTimeout = setTimeout(async () => {
        const response = await fetch(`/apis/api-city-suggestions.php?city=${query}`);
        const cities = await response.json();

        suggestionsContainer.innerHTML = '';

        cities.forEach(city => {
            if (selectedCities.includes(city)) return;

            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerText = city;

            div.onmousedown = (e) => {
                e.preventDefault();
                selectCity(city);
            };

            suggestionsContainer.appendChild(div);
        });
    }, 200);
}

function selectCity(city) {
    if (!selectedCities.includes(city)) {
        selectedCities.push(city);
        renderPills();
        filter();
    }

    document.getElementById('city-input').value = '';
    document.getElementById('city-suggestions').innerHTML = '';
}

function removeCity(city) {
    selectedCities = selectedCities.filter(c => c !== city);
    renderPills();
    filter();
}

function renderPills() {
    const container = document.getElementById('city-pills');
    container.innerHTML = '';

    selectedCities.forEach(city => {
        const pill = document.createElement('div');
        pill.className = 'city-pill';
        pill.innerHTML = `
            <span>${city}</span>
            <button type="button" onclick="removeCity('${city}')">&times;</button>
        `;
        container.appendChild(pill);
    });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.city-search-group')) {
        document.getElementById('city-suggestions').innerHTML = '';
    }
});

const map = L.map('map').setView([55.67960020013266, 12.56464935119663], 7);

L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO',
    subdomains: 'abcd',
    maxZoom: 18
}).addTo(map);

const markers = L.markerClusterGroup({
    disableClusteringAtZoom: 15,
    spiderfyOnMaxZoom: true,
    maxClusterRadius: 100
});

map.addLayer(markers);

const items = window.INITIAL_ITEMS || [];
let currentItems = items;
let selectedItemPk = null;
let isPropertyDetailOpen = false;
let isProgrammaticMapMove = false;
const itemsPerPage = 24;
let visibleItemsPage = 1;
let relatedVisibleItemsPage = 1;

function renderMarkers(itemsToRender) {
    markers.clearLayers();

    itemsToRender.forEach(item => {
        const marker = L.marker([
            parseFloat(item.item_lat),
            parseFloat(item.item_lon)
        ], {
            icon: L.divIcon({
                className: '',
                html: `
                    <button
                        class="marker ${item.item_type} ${item.item_pk === selectedItemPk ? 'selected' : ''}"
                        onclick="selectVisibleProperty('${escapeHtml(item.item_pk)}', ${parseFloat(item.item_lat)}, ${parseFloat(item.item_lon)}); return false;">
                    </button>
                `,
            }),
            item_pk: item.item_pk,
            zIndexOffset: item.item_pk === selectedItemPk ? 1000 : 0
        });

        markers.addLayer(marker);
    });
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}

function capitalizeFirstLetter(value) {
    const text = String(value ?? '');
    return text.charAt(0).toUpperCase() + text.slice(1);
}

function getGoogleMapsUrl(item) {
    const address = `${item.item_road_name} ${item.item_house_number}, ${item.item_zip_code} ${item.item_city_name}`;
    return `https://www.google.com/maps/place/${encodeURIComponent(address)}`;
}

function getPropertyUrl(item) {
    const toSlugPart = (value) => encodeURIComponent(String(value ?? '').trim().replace(/\s+/g, '_'));
    const address = `${toSlugPart(item.item_road_name)}-${toSlugPart(item.item_house_number)}-${toSlugPart(item.item_zip_code)}-${toSlugPart(item.item_city_name)}`;
    return `/house/${address}`;
}

function zoomToProperty(lat, lon) {
    isProgrammaticMapMove = true;
    map.once('moveend', () => {
        isProgrammaticMapMove = false;
    });

    map.setView([lat, lon], 16, {
        animate: true
    });
}

function scrollVisibleItemsToTop() {
    document.getElementById('visible-items-list')?.scrollTo({
        top: 0,
        behavior: 'auto'
    });
}

async function selectVisibleProperty(itemPk, lat, lon) {
    selectedItemPk = itemPk;
    isPropertyDetailOpen = true;
    renderMarkers(currentItems);
    zoomToProperty(lat, lon);
    await mix_fetch(`/apis/api-get-item.php?item_pk=${itemPk}`, 'GET', null, false);
    renderVisibleItemsBelowSelected(itemPk);
    scrollVisibleItemsToTop();
}

function getVisibleItems() {
    const bounds = map.getBounds();

    return currentItems.filter(item => {
        return bounds.contains([
            parseFloat(item.item_lat),
            parseFloat(item.item_lon)
        ]);
    });
}

function renderVisibleItemCard(item) {
    return `
            <article class="property-card visible-item"
                onclick="selectVisibleProperty('${escapeHtml(item.item_pk)}', ${parseFloat(item.item_lat)}, ${parseFloat(item.item_lon)})">

                <div class="property-image">
                    <img
                        src="${escapeHtml(item.item_main_image_path && item.item_main_image_path !== '0' ? item.item_main_image_path : '/static/images/sofa_dummy.png')}"
                        alt="Image of a ${escapeHtml(item.item_type || 'property')}"
                        onerror="this.src='/static/images/sofa_dummy.png'">
                </div>

                <div class="property-content">
                    <p class="property-type">
                        ${escapeHtml(capitalizeFirstLetter(item.item_type))}
                        ${item.item_energy_label && item.item_energy_label !== '0' ? `| Energy label ${escapeHtml(item.item_energy_label)}` : ''}
                    </p>

                    <h3 class="property-price">${Number(item.item_price).toLocaleString()} kr.</h3>

                    <h4 class="property-address">
                        ${escapeHtml(item.item_road_name)} ${escapeHtml(item.item_house_number)},
                        ${escapeHtml(item.item_zip_code)} ${escapeHtml(item.item_city_name)}
                    </h4>

                    <div class="property-info">
                        <div>
                            <span>Rooms</span>
                            <strong>${escapeHtml(item.item_number_of_rooms)}</strong>
                        </div>

                        <div>
                            <span>Area</span>
                            <strong>${escapeHtml(item.item_floor_square_meters)} m²</strong>
                        </div>
                    </div>

                    <div class="property-actions">
                        <a
                            class="primary-btn"
                            href="${escapeHtml(getGoogleMapsUrl(item))}"
                            target="_blank"
                            onclick="event.stopPropagation()">
                            Maps
                        </a>

                        <a
                            class="secondary-btn"
                            href="${escapeHtml(getPropertyUrl(item))}"
                            onclick="event.stopPropagation()">
                            View property
                        </a>
                    </div>
                </div>
            </article>
        `;
}

function renderLoadMoreButton(onclick) {
    return `
        <button
            type="button"
            class="secondary-btn load-more-visible"
            onclick="event.stopPropagation(); ${onclick}">
            Show next 24
        </button>
    `;
}

function renderVisibleItemsPage() {
    const aside = document.getElementById('visible-items-list');
    const visibleItems = getVisibleItems();
    const shownItems = visibleItems.slice(0, visibleItemsPage * itemsPerPage);
    const hasMoreItems = shownItems.length < visibleItems.length;

    aside.innerHTML = `
        ${shownItems.map(renderVisibleItemCard).join('')}
        ${hasMoreItems ? renderLoadMoreButton('showNextVisibleItems()') : ''}
    `;
}

function showNextVisibleItems() {
    visibleItemsPage++;
    renderVisibleItemsPage();
}

function updateVisibleItemsList() {
    if (isPropertyDetailOpen) {
        return;
    }

    visibleItemsPage = 1;
    renderVisibleItemsPage();
}

function renderVisibleItemsBelowSelected(selectedPk) {
    relatedVisibleItemsPage = 1;
    renderRelatedVisibleItemsPage(selectedPk);
}

function renderRelatedVisibleItemsPage(selectedPk) {
    const aside = document.getElementById('visible-items-list');
    document.getElementById('related-visible-items')?.remove();

    const visibleItems = getVisibleItems().filter(item => item.item_pk !== selectedPk);
    const shownItems = visibleItems.slice(0, relatedVisibleItemsPage * itemsPerPage);
    const hasMoreItems = shownItems.length < visibleItems.length;

    aside.insertAdjacentHTML('beforeend', `
        <div id="related-visible-items">
            ${shownItems.map(renderVisibleItemCard).join('')}
            ${hasMoreItems ? renderLoadMoreButton(`showNextRelatedVisibleItems('${escapeHtml(selectedPk)}')`) : ''}
        </div>
    `);
}

function showNextRelatedVisibleItems(selectedPk) {
    relatedVisibleItemsPage++;
    renderRelatedVisibleItemsPage(selectedPk);
}

function update_map_items(response) {
    const data = typeof response === 'string' ? JSON.parse(response) : response;
    currentItems = Array.isArray(data.items) ? data.items : [];
    selectedItemPk = null;
    isPropertyDetailOpen = false;

    renderMarkers(currentItems);
    updateVisibleItemsList();

    if (currentItems.length > 0) {
        map.fitBounds(markers.getBounds());
    }

    if (data.url) {
        const params = new URLSearchParams();

        data.url.forEach(obj => {
            const key = Object.keys(obj)[0];
            params.set(key, obj[key]);
        });

        window.history.pushState({}, "", "?" + params.toString());
    }
}

map.on('dragstart zoomstart', () => {
    if (!isProgrammaticMapMove) {
        isPropertyDetailOpen = false;
    }
});

map.on('moveend zoomend', updateVisibleItemsList);

renderMarkers(currentItems);
updateVisibleItemsList();
