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
  var countries = ["Płowiecka 5b 04-501",
"Wiatraczna 9 lok. 1 04-366",
"Chłopickiego 11/13 lok. U8 04-314",
"Igańska 11 04-087",
"Międzyborska 50 04-041",
"Nagórskiego 7 03-982",
"Zamoyskiego 47 03-801",
"Szczygla 8 00-391",
"Lotaryńska 48 lok. 103A 03-970",
"Franciszkańska 6 00-214",
"Siennicka 23/73 04-394",
"Wilcza 27 lok. LU-68.03 00-544",
"Kobielska 6 lok. U5 04-359",
"Walecznych 68/21 03-926",
"Mokra 33 03-562",
"Rechniewskiego 11/4 i 5 03-980",
"Grochowska 94 04-301",
"Radzymińska 59A/102 03-751",
"Starzyńskiego 10 lok.V 03-456",
"Dąbrowszczaków 5 03-476",
"G.Bora Komorowskiego 56A/U3 03-982",
"Motycka 23 lok. U1 03-566",
"Jagiellońska 58/129 03-715",
"Generała S. Skalskiego 3 lok. U4 03-982",
"Stanów Zjednoczonych 72/13A 04-036",
"Grochowska 304 lok.65 03-840",
"Gocławska 9/U7 03-810",
"Majdańska 7 04-088",
"Igańska 26 04-083",
"Świętego Wincentego 16/U1 03-505",
"Jugosłowiańska 15 lok. LU10 03-984",
"Szaserów 105A 04-335",
"Piotra Skargi 61/U1A 03-516",
"Jana Nowaka-Jeziorańskiego 49/U17 03-982",
"Handlowa 34 lok U-1 03-556",
"Zamiejska 10 03-580",
"Lusińska 21 lok. U1 03-569",
"Szaserów 29 lok. U2 04-306",
"Trocka 11 03-563",
"Osiecka 49 lok. U2/U3/U4 04-173",
"Czerniakowska 178 lok. LU1 00-440",
"Komorska 11/15 lok. 4 04-161",
"Miodowa 23 00-246",
"Tamka 29 lok. U01 00-355",
"Orłowicza 12 lok. U3 00-414",
"Mińska 25C lok. U8 03-808",
"Jerozolimskie 42 lok. LU7 00-024",
"Grochowska 170A lok. 1 04-357",
"Nowy Świat 35 lok. U1 00-029",
"Kopernika 21 00-359",
"Jerozolimskie 29 lok. LU2 00-508",
"Grochowska 14A 04-217",
"Górnośląska 7A /LU2B 00-443",
"Markowska 22 lok. U02 03-742",
"Wilcza 33 lok. LU3 00-544",
"Szanajcy 11 lok. 221 03-481",
"Solec 18 lok. U3 00-410",
"Okrzei 18 lok. U6 03-710",
"SASKA 16 lok. U4 03-968",
"Moniuszki 1A 00-014",
"Poligonowa 1 lok. 3 04-051",
"Kowieńska 3 03-438",
"Krucza 46 lok. LU3 00-509",
"Solidarności 68 lok. 5U-39 00-240",
"Garibaldiego 5 lok. U1 04-078",
"Wiejska 20 lok. B 00-490",
"al.Solidarności 3-St.Dw.WileńskiC15 03-734",
"Żurawia 18 00-515",
"Nowaka Jeziorańskiego 53A lok.U4 03-982",
"Chmielna 11 lok. 4 00-021",
"Strzelecka 27/29 Lok.8 03-433",
"Goławicka 1A i 1B 03-550",
"Żupnicza 20 lok. U1 03-821",
"Gen. R. Abrahama 18 Lok. nr 303 i 304 03-982",
"Kaleńska 5 04-367",
"Bora-Komorowskiego 12G 03-982",
"Witebska 6 lok. U1 03-507",
"Targowa 21 lok. LU1 03-728",
"Radzymińska 12 Lok. U1 03-752",
"Piękna 28/34 lok. LU2 00-547",
"Ząbkowska 35 03-736",
"Okrzei 1a Lok. 1 03-715",
"ul.Komorska 29/33 Lok.2 04-161",
"Płosa 2 Lok. U1 03-531",
"Meissnera 1/3 Lok.315 03-982",
"Przeworska 3 04-382",
"Międzyborska 8A Lok. U2 04-041",
"Motorowa 10 Lok. U4 04-035",
"Nowy Świat 64 00-357",
"Grenadierów nr 30B lok. U1 i U2 04-062",
"Długa 31 lok. A 00-238",
"Ostrobramska 73E lok. U3 i U4 04-175",
"KOKORYCZKI 3A LOK. U4 04-191",
"Grochowska 129 04-148",
"Chełmżyńska 198 lok. U3 04-464",
"Boremlowska 48 lok. U1,U2 i U3 04-347",
"Łukowska 18 lok. U2 04-133",
"Marszałkowska 84/92 00-517",
"Markowska 3 lok. U2 03-742",
"Budowlana 7 lok. U6 03-315",
"Foksal 12/14 lok.18 00-366",
"Tarnowiecka 13 LOK.1 04-174",
"Szanajcy 18 lok. 45 03-481",
"Montwiłłowska 12 LOK. U2,U3 03-890",
"Ząbkowska 14 LOK.U4 03-735",
"Stacja Metra C-18 Trocka nr 1014 03-580",
"Wrzesińska 4 Lok. U2 i U3 03-713",
"Grochowska 56 lok. U1 04-282",
"Rembrandta 2 lok. U14 03-531",
"Łukowska 46 UŻ/4 04-133",
"Mińska 12 Lok. U1 03-808",
"BIAŁOSTOCKA 11 03-748",
"Rodziewiczówny 1 Lok. U6 04-187",
"Corazziego 4 lok.3 00-087",
"Remiszewska 1 LOK.10B-U 03-550",
"Gocławska 9B lok. U11 03-810",
"św. Wincentego 66 LOK. U1 03-531",
"Garibaldiego 4 lok. 10P 04-078",
"Świętokrzyska 31/33A lok.3 00-049",
"Jórskiego 20 03-584",
"Targowa 80/82 03-448",
"Kilińskiego 3 00-257",
"Terespolska 4 509 i 510 03-813",
"Topiel 12 00-342",
"Waszyngtona 98A 04-015",
"T. Bora Komorowskiego 56Blok-U20 03-982",
"Chełmżyńska 12 04-247",
"J. Nowaka Jeziorańskiego 8 LU1i LU2 03-984",
"Międzyborska 15 lok. 1a 04-041",
"Chrzanowskiego 4 lok. U13 i U14 04-381",
"Piwna 7 lok. LU1 00-265",
"Montwiłłowska 1 lok.U3 i U4 03-890",
"Przeworska 7 lok.U1 04-382",
"pl. Hallera 8 lok. 3A 03-464",
"Radzymińska 123 lok. U2 03-560",
"Hoża 5/7 lok 11 00-528",
"Brzeska 10 lok.U2 i U3 03-737",
"Senatorska 2 00-075",
"Chełmżyńska 198B lok. U1 04-464",
"Hoża 40 lok. LU8 00-516",
"Chrzanowskiego 14 04-392",
"Dobra 32 lok. 1 00-344",
"Targowa 41 lok 50/52 03-728",
"Kakowskiego 8 lok.U1 04-042",
"Konopacka 19 lok. U.1 03-428",
"Zgoda 9 lok. 3 00-018",
"Ossowskiego 11 LOK. U1, U13 03-542",
"Targowa 34 lok. LU1 03-733",
"SZWEDZKA 30 LOK. U7 03-420",
"MARKOWSKA 10 LOK. LU01 . 03-742",
"WILEŃSKA 14A LOK. U3 03-414",
"ZGODA 13 lok. 4 00-018",
"PODSKARBIŃSKA 30 lok. U2 03-829",
"BORZYMOWSKA 21 03-565",
"WARSZAWSKI ŚWIT 5 03-368",
"UL.WSPÓLNA DROGA 21 lok.U2 (nr wew) 04-345",
"PODSKARBIŃSKA 21 LOK. U8 03-829",
"ELEKTRYCZNA 2 LOK. U5 00-347",
"TARGOWA 10 LOK. U10 . 03-731",
"MIŃSKA 45 LOK. 201 . 03-808",
"KRUCZA 41/43 LOK. LU2 . 00-509",
"UMIŃSKIEGO 6 LOK. 203 . 03-984",
"RADZYMIŃSKA 33 lok. 1 03-747",
"KOTSISA 8A LOK. 1 03-307",
"CELOWNICZA 4 LOK. U2 04-175",
"KOBIELSKA 19 LOK. II 04-352",
"KOWIEŃSKA 24 LOK. U2 03-438",
"BOLESŁAWICKA 9 03-352",
"ZWYCIĘZCÓW 4 03-941",
"KAWĘCZYŃSKA 37 03-775",
"WYSOCKIEGO 6D 03-371",
"ŁUKOWSKA 1 LOK. U13 04-113",
"JAGIELLOŃSKA 75 LOK. U1 i U2 03-215",
"RADZYMIŃSKA 49 LOK. U1 03-751",
"OSZMIAŃSKA 20 03-503",
"BORA KOMOROWSKIEGO 37 LOK.205 . 03-982",
"BONIFRATERSKA 10A LOK. U1 . 00-213",
"TARGOWA 59 03-729",
"KŁOPOTOWSKIEGO 11 LOK. U6. 03-718",
"GRENADIERÓW 11 LOK. 2 04-052",
"BLISKA 17 LOK. U1 03-804",
"OPTYKÓW 3B LOK. U01 . 04-175",
"NOWAKA JEZIORAŃSKIEGO 4 LOK. U2 . 03-984",
"ROŻNOWSKA 13 LOK. U1 . 04-213",
"BORA KOMOROWSKIEGO 4 LOK. 201A . 03-982",
"CHMIELNA 35 00-020",
"MIŃSKA 69 LOK. U4 . 03-828",
"OSTRZYCKA 2/4 04-043",
"ŻUPNICZA 23 lok. U1 . 03-821",
"Trocka 1 03-563",
"Zwycięzców 28 lok. III/7 03-945",
"Zamieniecka 61 04-158",
"KLUKOWSKA 54 LOK. U1 03-892",
"Zamkowa 8 lok. U1 . 03-890",
"Plac Hallera 4 lok. 44 03-464",
"KRUSZEWSKIEGO 14 LOK. U1 I U2 04-079",
"GROCHOWSKA 87 LOK. U6 . 04-186",
"Abrahama 1A . 03-982",
"PODSKARBIŃSKA 34 LOK U4 03-829",
"Wiejska 9 LOK. C 00-480",
"Solec 22 00-410",
"URUGWAJSKA 2 LOK. A/U3 . 03-969",
"Jugosłowiańska 19 03-984",
"Radzymińska 96 03-512",
"Grochowska 19 04-186",
"Wał Miedzeszyński 670 03-994",
"Gen. Augusta Emila Fieldorfa 41A 04-125",
"Grochowska 149/151 04-139",
"Aleja Solidarności 13 03-412",
"Św. Wincentego 4 03-505",
"Ostrobramska 81 04-175",
"11 Listopada Inmedio Trendy 11 Listopada 10, 03-435 03-435",
"Terespolska 4 Inmedio Trendy Terespolska 4, 03-813 03-813",
"Pl. Konesera 10A 03-736",
"Al. Solidarności 6 03-411",
"Odrowąża 7A  03-310",
"Jagiellońska 78 03-301",
"MARSZAŁKOWSKA 138",
"JEROZOLIMSKIE 11/19",
"Dobra 17",
"PLAC BANKOWY 4",
"Zagorna 16",
"SWIETOKRZYSKA 20",
"ANDERSA 24",
"KOŹMIŃSKA 16",
"Smolna 11",
"Marokańska 16",
"Szymanowskiego 8",
"Inzynierska 15",
"GARWOLIŃSKA 5",
"TROCKA 6/77",
"KRAKOWSKIE PRZEDMIEŚCIE 6",
"Swietokrzyska 14",
"RADNA 2/4",
"GÓRNOŚLĄSKA 4 A",
"ROGALSKIEGO 1 D",
"SOLEC 81",
"INZYNIERSKA/11-LISTOPADA",
"GROCHOWSKA 332 LOK. 3",
"STRZELECKA 21/25 LOK. 2",
"TARGOWA 62",
"Osowska 76C",
"WASZYNGTONA 100 A LOK 1",
"BORA-KOMOROWSKIEGO 5 E",
"WAŁOWA 7",
"Lipska 15A",
"BISKUPIA 50",
"SENATORSKA 24",
"SKOCZYLASA 10/12 LOK. 79",
"KIJOWSKA 11",
"Rozlucka 5A",
"ELEKTRYCZNA 2A",
"REMBIELIŃSKA NR 7A PAW.2 BAZAREK",
"OSTROBRAMSKA 83 LOK. 4B",
"CORAZZIEGO 2",
"GROCHOWSKA 202",
"MIEDZYNARODOWA 62",
"SMOLENSKA 83",
"REMISZEWSKA 1 LOK.10A",
"Skoczylasa 5a",
"JOZEFA SZANAJCY 18 LOK. 44",];
  
  /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
  autocomplete(document.getElementById("myInput"), countries);