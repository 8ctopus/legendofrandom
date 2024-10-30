export function filterTables(selector) {
  const tables = document.querySelectorAll(selector);

  tables.forEach(function (table) {
    let timerId;

    table.addEventListener('keyup', function (event) {
      if (timerId) {
        clearTimeout(timerId);
      }

      //console.log(event.target.dataset.table);

      // wait before filtering
      timerId = setTimeout(filterTable(event.target.dataset.table, event.target.value), 300);
    });
  });
}

/**
 * Filter table records
 *
 * @param  {string} table
 * @param  {string} search
 *
 * @return {void}
 */
export function filterTable(table, search) {
  const rows = document.querySelectorAll(`#${table} tbody tr`);
  const length = rows.length;

  for (let i = 0; i < length; i++) {
    const text = rows[i].innerText;

    if (text.indexOf(search) === -1) {
        rows[i].style.display = 'none';
    } else {
        rows[i].style.display = '';
    }
  }
}
