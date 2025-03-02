/* i took this script from https://www.w3schools.com/howto/howto_js_autocomplete.asp 
i dont like js... */

function autocomplete(inp, arr) 
{
    var currentFocus;
    inp.addEventListener("input", function(e) 
    {
        var a, b, i, val = this.value;
        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
          /*check if the item starts with the same letters as the text field value:*/
          if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
            /*create a DIV element for each matching element:*/
            b = document.createElement("DIV");
            /*make the matching letters bold:*/
            b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
            b.innerHTML += arr[i].substr(val.length);
            /*insert a input field that will hold the current array item's value:*/
            b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
            /*execute a function when someone clicks on the item value (DIV element):*/
            b.addEventListener("click", function(e) {
                /*insert the value for the autocomplete text field:*/
                inp.value = this.getElementsByTagName("input")[0].value;
                /*close the list of autocompleted values,
                (or any other open lists of autocompleted values:*/
                closeAllLists();
            });
            a.appendChild(b);
          }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          /*If the arrow DOWN key is pressed,
          increase the currentFocus variable:*/
          currentFocus++;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 38) { //up
          /*If the arrow UP key is pressed,
          decrease the currentFocus variable:*/
          currentFocus--;
          /*and and make the current item more visible:*/
          addActive(x);
        } else if (e.keyCode == 13) {
          /*If the ENTER key is pressed, prevent the form from being submitted,*/
          e.preventDefault();
          if (currentFocus > -1) {
            /*and simulate a click on the "active" item:*/
            if (x) x[currentFocus].click();
          }
        }
    });
    function addActive(x) {
      /*a function to classify an item as "active":*/
      if (!x) return false;
      /*start by removing the "active" class on all items:*/
      removeActive(x);
      if (currentFocus >= x.length) currentFocus = 0;
      if (currentFocus < 0) currentFocus = (x.length - 1);
      /*add class "autocomplete-active":*/
      x[currentFocus].classList.add("autocomplete-active");
    }
    function removeActive(x) {
      /*a function to remove the "active" class from all autocomplete items:*/
      for (var i = 0; i < x.length; i++) {
        x[i].classList.remove("autocomplete-active");
      }
    }
    function closeAllLists(elmnt) {
      /*close all autocomplete lists in the document,
      except the one passed as an argument:*/
      var x = document.getElementsByClassName("autocomplete-items");
      for (var i = 0; i < x.length; i++) {
        if (elmnt != x[i] && elmnt != inp) {
          x[i].parentNode.removeChild(x[i]);
        }
      }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
  }
  
  /*An array containing all the country names in the world:*/
  var countries = [
    "Płowiecka 5b",
    "Wiatraczna 9",
    "Chłopickiego 11/13",
    "Igańska 11",
    "Międzyborska 50",
    "Nagórskiego 7",
    "Zamoyskiego 47",
    "Szczygla 8",
    "Lotaryńska 48",
    "Franciszkańska 6",
    "Siennicka 23",
    "Wilcza 27",
    "Kobielska 6",
    "Walecznych 68",
    "Mokra 33",
    "Rechniewskiego 11",
    "Grochowska 94",
    "Radzymińska 59A",
    "Starzyńskiego 10",
    "Dąbrowszczaków 5",
    "Bora Komorowskiego 56A",
    "Motycka 23",
    "Jagiellońska 58",
    "Generała S. Skalskiego 3",
    "Stanów Zjednoczonych 72",
    "Grochowska 304",
    "Gocławska 9",
    "Majdańska 7",
    "Igańska 26",
    "Świętego Wincentego 16",
    "Jugosłowiańska 15",
    "Szaserów 105A",
    "Piotra Skargi 61",
    "Jana Nowaka-Jeziorańskiego 49",
    "Handlowa 34",
    "Zamiejska 10",
    "Lusińska 21",
    "Szaserów 29",
    "Trocka 11",
    "Osiecka 49",
    "Czerniakowska 178",
    "Komorska 11/15",
    "Miodowa 23",
    "Tamka 29",
    "Orłowicza 12",
    "Mińska 25C",
    "Jerozolimskie 42",
    "Grochowska 170A",
    "Nowy Świat 35",
    "Kopernika 21",
    "Jerozolimskie 29",
    "Grochowska 14A",
    "Górnośląska 7A",
    "Markowska 22",
    "Wilcza 33",
    "Szanajcy 11",
    "Solec 18",
    "Okrzei 18",
    "Saska 16",
    "Moniuszki 1A",
    "Poligonowa 1",
    "Kowieńska 3",
    "Krucza 46",
    "Solidarności 68",
    "Garibaldiego 5",
    "Wiejska 20",
    "Żurawia 18",
    "Nowaka Jeziorańskiego 53A",
    "Chmielna 11",
    "Strzelecka 27/29",
    "Goławicka 1A",
    "Żupnicza 20",
    "Abrahama 18",
    "Kaleńska 5",
    "Bora-Komorowskiego 12G",
    "Witebska 6",
    "Targowa 21",
    "Radzymińska 12",
    "Piękna 28/34",
    "Ząbkowska 35",
    "Okrzei 1A",
    "Komorska 29/33",
    "Płosa 2",
    "Meissnera 1/3",
    "Przeworska 3",
    "Międzyborska 8A",
    "Motorowa 10",
    "Nowy Świat 64",
    "Grenadierów 30B",
    "Długa 31",
    "Ostrobramska 73E",
    "Kokoryczki 3A",
    "Grochowska 129",
    "Chełmżyńska 198",
    "Boremlowska 48",
    "Łukowska 18",
    "Marszałkowska 84/92",
    "Markowska 3",
    "Budowlana 7",
    "Foksal 12/14",
    "Tarnowiecka 13",
    "Szanajcy 18",
    "Montwiłłowska 12",
    "Ząbkowska 14",
    "Stacja Metra C-18 Trocka",
    "Wrzesińska 4",
    "Grochowska 56",
    "Rembrandta 2",
    "Łukowska 46",
    "Mińska 12",
    "Białostocka 11",
    "Rodziewiczówny 1",
    "Corazziego 4",
    "Remiszewska 1",
    "Gocławska 9B",
    "Św. Wincentego 66",
    "Garibaldiego 4",
    "Świętokrzyska 31/33A",
    "Jórskiego 20",
    "Targowa 80/82",
    "Kilińskiego 3",
    "Terespolska 4",
    "Topiel 12",
    "Waszyngtona 98A",
    "Bora Komorowskiego 56B",
    "Chełmżyńska 12",
    "J. Nowaka Jeziorańskiego 8",
    "Międzyborska 15",
    "Chrzanowskiego 4",
    "Piwna 7",
    "Montwiłłowska 1",
    "Przeworska 7",
    "Pl. Hallera 8",
    "Radzymińska 123",
    "Hoża 5/7",
    "Brzeska 10",
    "Senatorska 2",
    "Chełmżyńska 198B",
    "Hoża 40",
    "Chrzanowskiego 14",
    "Dobra 32",
    "Targowa 41",
    "Kakowskiego 8",
    "Konopacka 19",
    "Zgoda 9",
    "Ossowskiego 11",
    "Targowa 34",
    "Szwedzka 30",
    "Markowska 10",
    "Wileńska 14A",
    "Zgoda 13",
    "Podskarbińska 30",
    "Borzymowska 21",
    "Warszawski Świt 5",
    "Wspólna Droga 21",
    "Podskarbińska 21",
    "Elektryczna 2",
    "Targowa 10",
    "Mińska 45",
    "Krucza 41/43",
    "Umińskiego 6",
    "Radzymińska 33",
    "Kotsisa 8A",
    "Celownicza 4",
    "Kobielska 19",
    "Kowieńska 24",
    "Bolesławicka 9",
    "Zwycięzców 4",
    "Kawęczyńska 37",
    "Wysockiego 6D",
    "Łukowska 1",
    "Jagiellońska 75",
    "Radzymińska 49",
    "Oszmiańska 20",
    "Bora Komorowskiego 37",
    "Bonifraterska 10A",
    "Targowa 59",
    "Kłopotowskiego 11",
    "Grenadierów 11",
    "Bliska 17",
    "Optyków 3B",
    "Nowaka Jeziorańskiego 4",
    "Rożnowska 13",
    "Bora Komorowskiego 4",
    "Chmielna 35",
    "Mińska 69",
    "Ostrzycka 2/4",
    "Żupnicza 23",
    "Trocka 1",
    "Zwycięzców 28",
    "Zamieniecka 61",
    "Klukowska 54",
    "Zamkowa 8",
    "Plac Hallera 4",
    "Kruszewskiego 14",
    "Grochowska 87",
    "Abrahama 1A",
    "Podskarbińska 34",
    "Wiejska 9",
    "Solec 22",
    "Urugwajska 2",
    "Jugosłowiańska 19",
    "Radzymińska 96",
    "Grochowska 19",
    "Wał Miedzeszyński 670",
    "Gen. Augusta Emila Fieldorfa 41A",
    "Grochowska 149/151",
    "Aleja Solidarności 13",
    "Św. Wincentego 4",
    "Ostrobramska 81",
    "11 Listopada 10",
    "Terespolska 4",
    "Pl. Konesera 10A",
    "Al. Solidarności 6",
    "Odrowąża 7A",
    "Jagiellońska 78",
    "Marszałkowska 138",
    "Jerozolimskie 11/19",
    "Dobra 17",
    "Plac Bankowy 4",
    "Zagórna 16",
    "Świętokrzyska 20",
    "Andersa 24",
    "Koźmińska 16",
    "Smolna 11",
    "Marokańska 16",
    "Szymanowskiego 8",
    "Inżynierska 15",
    "Garwolińska 5",
    "Trocka 6",
    "Krakowskie Przedmieście 6",
    "Świętokrzyska 14",
    "Radna 2",
    "Górnośląska 4A",
    "Rogalskiego 1D",
    "Solec 81",
    "Inżynierska 11",
    "Grochowska 332",
    "Strzelecka 21",
    "Targowa 62",
    "Osowska 76C",
    "Waszyngtona 100A",
    "Bora-Komorowskiego 5E",
    "Wałowa 7",
    "Lipska 15A",
    "Biskupia 50",
    "Senatorska 24",
    "Skoczylasa 10",
    "Kijowska 11",
    "Rozłucka 5A",
    "Elektryczna 2A",
    "Rembielińska 7A",
    "Ostrobramska 83",
    "Corazziego 2",
    "Grochowska 202",
    "Międzynarodowa 62",
    "Smoleńska 83",
    "Remiszewska 1",
    "Skoczylasa 5A",
    "Józefa Szanajcy 18"];
  
  /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
  autocomplete(document.getElementById("myInput"), countries);