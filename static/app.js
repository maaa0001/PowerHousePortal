function clear_markers(data) {
  console.log(data);
  const urlParams = new URLSearchParams(window.location.search);
  // urlParams.set('beds', '2')
  // urlParams.set('baths', '1')
  //window.history.replaceState({}, '', `?${urlParams.toString()}`)

  // ?beds=2&baths=1

  markers.clearLayers();
  data = JSON.parse(data);
  console.log(data.url);
  data.url.forEach((prop) => {
    console.log(Object.keys(prop)[0]); // beds | baths
    let key = Object.keys(prop)[0]; // beds | baths
    console.log(prop[key]); //
    let value = prop[key]; // 2 | 1
    urlParams.set(key, value);
  });
  window.history.replaceState({}, "", `?${urlParams.toString()}`);

  // items = data.items
  // console.log(items)
  data.items.forEach((item) => {
    if (item.type == "villa") {
      var marker = L.marker([item.lat, item.lon], {
        icon: L.divIcon({
          className: "",
          html: `
                        <button 
                            class="marker ${item.type}" onclick="mixhtml(); return false;"
                            mix-get="api-get-item?item_pk=${item.pk}">
                        </button>
                    `,
        }),
        item_pk: item.pk,
      });
    } else if (item.type == "condo") {
      var marker = L.marker([item.lat, item.lon], {
        icon: L.divIcon({
          className: "",
          html: `
                        <button 
                            class="marker ${item.type}" onclick="mixhtml(); return false;"
                            mix-get="api-get-item?item_pk=${item.pk}">
                        </button>
                    `,
        }),
        item_pk: item.pk,
      });
    } else {
      var marker = L.marker([item.lat, item.lon], {
        icon: L.divIcon({
          className: "",
          html: `
                        <button 
                            class="marker ${item.type}" onclick="mixhtml(); return false;"
                            mix-get="api-get-item?item_pk=${item.pk}">
                        </button>
                    `,
        }),
        item_pk: item.pk,
      });
    }
    markers.addLayer(marker);
  });
  map.addLayer(markers);
}


const toggle = document.getElementById('themeToggle');
const root = document.documentElement;

const saved = localStorage.getItem('theme');
if (saved) {
  root.setAttribute('data-theme', saved);
  toggle.textContent = saved === 'dark' ? '☀️' : '🌙';
}

toggle.addEventListener('click', () => {
  const current = root.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  root.setAttribute('data-theme', next);
  localStorage.setItem('theme', next);
  toggle.textContent = next === 'dark' ? '☀️' : '🌙';
});