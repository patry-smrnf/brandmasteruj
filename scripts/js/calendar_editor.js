function scrollToTop() {
    console.log("s")
    window.scrollTo({
      top: 0,
      behavior: "smooth" // Enables smooth scrolling
    });
  }

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function set_editor(name, start, end, data)
{
  scrollToTop();

    var editor_panel_address_input = document.getElementById("myInput");
    var editor_panel_start = document.getElementById("punkt_godzina_start");
    var editor_panel_koniec = document.getElementById("punkt_godzina_koniec");
    var editor_panel_data_input = document.getElementById("data_input");

    var editor_panel_data = document.getElementById("punkt_data");


    console.log(data);
    editor_panel_address_input.value = name;
    editor_panel_start.value = start;
    editor_panel_koniec.value = end;
    editor_panel_data_input.value = data;

    editor_panel_data.innerText = formatDate(data);

}