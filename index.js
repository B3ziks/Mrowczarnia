//filter, wyszukiwarka
function filterAnts() {
  var input, filter, items, itemName, i;
  input = document.getElementById('searchInput');
  filter = input.value.toUpperCase();
  items = document.getElementById("antList").getElementsByClassName("item");

  // loopuje wszystkie elementy listy i ukrywa te, które nie pasują do wyszukiwanego hasła
  for (i = 0; i < items.length; i++) {
    itemName = items[i].getElementsByClassName("item-name")[0];
    if (itemName) {
      txtValue = itemName.textContent || itemName.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        items[i].style.display = "";
      } else {
        items[i].style.display = "none";
      }
    }
  }
}