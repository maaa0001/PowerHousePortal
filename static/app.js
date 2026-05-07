function clear_markers(data) {
  console.log("clear_markers called with data:", data);
  const urlParams = new URLSearchParams();

  markers.clearLayers();
  data = JSON.parse(data);
  console.log(data.url);
  data.url.forEach((prop) => {
    let key = Object.keys(prop)[0];
    let value = prop[key];
    urlParams.set(key, value);
  });
  window.history.replaceState({}, "", `?${urlParams.toString()}`);

  data.items.forEach((item) => {
    var marker = L.marker([item.item_lat, item.item_lon], {
      icon: L.divIcon({
        className: "",
        html: `
                        <button 
                            class="marker ${item.item_type}" onclick="mixhtml(); return false;"
                            mix-get="api-get-item?item_pk=${item.item_pk}">
                        </button>
                    `,
      }),
      item_pk: item.item_pk,
    });
    markers.addLayer(marker);
  });
  map.addLayer(markers);

  // Zoom to fit the markers if there are any
  if (data.items.length > 0) {
    map.fitBounds(markers.getBounds(), { padding: [50, 50], maxZoom: 15 });
  }
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