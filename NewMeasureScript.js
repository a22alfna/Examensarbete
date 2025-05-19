// ==UserScript==
// @name        Exjobb nytt mätscript
// @namespace   Exjobb
// @description byt ut match url till elasticsearch sidan och ändra getElementById mellan artist och fullText och ändra submit button till SearchKeyword eller SearchFulltext
// @match       http://localhost/Examensarbete/ElasticConnect.php
// @version     4
// @grant       GM_xmlhttpRequest
// @run-at      document-idle
// @require     https://unpkg.com/compromise@13.11.0/builds/compromise.min.js
// @require     https://raw.githubusercontent.com/LenaSYS/ContextFreeLib/master/js/contextfreegrammar.js
// @require     https://raw.githubusercontent.com/LenaSYS/Random-Number-Generator/master/seededrandom.js
// ==/UserScript==

(function() {
    'use strict';

    /*** KONFIGURATION ***/
    let count = parseInt(localStorage.getItem('count') || 0, 10);
    let searchTerms = JSON.parse(localStorage.getItem('searchTerms') || "[]");
    let elapsedData = JSON.parse(localStorage.getItem('elapsedTimes') || "[]");

    const nrWords = 100;// Antal mätningar
    const seed = 10;
    const searchFieldId = "fullText";// ID på sökfält
    const searchButtonId = "SearchFulltext";// ID på sökknapp
    Math.setSeed((count % nrWords) + seed);
    /*** SLUT PÅ KONFIGURATION ***/



    function getRandomArtist() {
        const artists = [
            "Shakira", "Ricky Martin", "CNCO", "J Balvin, Pharrell Williams, BIA, Sky", "Daddy Yankee", "Sebastian Yatra", "Rombai", "Carlos Vives, Shakira", "Ozuna", "Daddy Yankee", "Thalia",
            "Maluma", "Charly Black, Daddy Yankee", "Piso 21", "Enrique Iglesias", "DJ Snake, Justin Bieber", "Mano Arriba", "Wisin", "Clean Bandit", "Chano", "IAmChino, El Chacal", "Drake, WizKid, Kyla", "Calvin Harris",
            "Major Lazer", "Gente De Zona", "The Weeknd, Daft Punk", "Cosculluela", "Maroon 5, Kendrick Lamar", "Bruno Mars", "Justin Timberlake", "Juanes", "J Balvin", "Martin Garrix, Bebe Rexha", "Los Bonnitos", "Mike Posner",
            "Reik", "Joey Montana", "Rihanna, Drake", "Marama, Rombai", "The Chainsmokers", "Nicky Jam", "Agapornis", "DNCE", "Farruko", "Marama", "The Chainsmokers, Halsey",
            "Shawn Mendes", "El Polaco", "Sebastian Yatra, Cosculluela, Cali Y El Dandee", "#TocoParaVos", "Pijama Party, Cande Buasso", "Starley", "Nicky Jam, Enrique Iglesias", "LOAN", "David Guetta, Cedric Gervais, Chris Willis", "Juan Magán, Luciana", "Rafaga", "Ozuna, Cosculluela, Revol",
            "Steve Aoki, Louis Tomlinson", "Ariana Grande, Nicki Minaj", "Xantos, Dynell", "Sia", "CantoParaBailar, Lucas Sugo", "Fifth Harmony", "Charlie Puth", "Ozuna, Bad Bunny, Farruko, Ñengo Flow", "Abel Pintos", "Jacob Forever", "Carlos Baute, Alexis y Fido", "Papichamp",
            "Kevin Roldan", "Mambo Kingz, DJ Luian, Luigi 21 Plus, Ozuna", "Los Totora", "aLee DJ, Zeta Music", "Enrique Iglesias, Descemer Bueno, Gente De Zona", "Sofía Reyes", "Klasico", "Cali Y El Dandee, Juan Magán, Sebastian Yatra", "Gilda", "Yandel, Egbert Rosa aka Haze", "Twenty One Pilots",
            "Justin Bieber", "Maná, Nicky Jam", "Kungs", "Amar Azul", "Danny Paz, El Villano", "El Pepo", "Plan B", "Anuel AA", "Rodrigo", "Pitbull, Sensato, Osmani Garcia \"La Voz\"",
            "Nevada, Mark Morrison, Fetty Wap", "Banda XXI", "Reykon", "Coldplay", "De La Ghetto", "Justin Quiles", "Los Palmeras", "KAROL G, Ozuna", "Marka Akme", "Antonio Rios", "Grupo Sombras", "El Villano",
            "J Balvin, Daddy Yankee", "Alkilados, Nicky Jam, J Alvarez, El Roockie", "Tambó Tambó", "La Beriso", "El Reja", "Justin Quiles, Yandel, Gadiel", "Los Charros", "Maxi Trusso", "Feid, J Balvin", "Corta La Bocha", "De La Ghetto, Mambo Kingz, DJ Luian",
            "Soda Stereo", "RC BAND", "Alejandro Sanz, Marc Anthony", "Pitbull, Ne-Yo", "Charly Black", "Jonas Blue, JP Cooper", "Lali", "Jambao", "Juan Magán", "Benjamín Amadeo", "Maroon 5", "ZAYN, Taylor Swift",
            "James Arthur", "NEIKED, Dyo", "Peking Duk", "Rae Sremmurd, Gucci Mane", "Andy Grammer", "Mura Masa", "Zara Larsson", "Alessia Cara", "Drake", "FRENSHIP, Emily Warren", "The Chainsmokers, Phoebe Ryan", "Hailee Steinfeld, Grey, Zedd",
            "The Vamps, Matoma", "Jonas Blue, RAYE", "Flume", "Illy", "Milky Chance", "Sage The Gemini", "Robin Schulz, David Guetta, Cheat Codes", "Mike Perry", "The Chainsmokers, XYLØ", "Cashmere Cat", "Galantis, Hook N Sling", "Flume, kai",
            "PNAU", "Calum Scott", "Childish Gambino", "Machine Gun Kelly", "Zay Hilfigerrr, Zayion McCall", "John Legend", "Noah Cyrus, Labrinth", "Charli XCX", "MØ", "Migos", "Shelley FKA DRAM", "The Weeknd",
            "MiC LOWRY", "Little Mix", "Joel Adams", "The xx", "Sofi Tukker", "Drake, Rihanna", "Rag'n'Bone Man", "PON CHO, Paige IV", "Adele", "Hilltop Hoods, Montaigne, Tom Thum", "J. Cole", "Desiigner",
            "The Veronicas", "Amy Shark", "Aminé", "Jonas Blue, Dakota", "Lukas Graham", "gnash", "Ariana Grande", "Alan Walker", "Lil Wayne", "Tove Lo", "Dante Klein, Cheat Codes", "Petit Biscuit",
            "Terror Jr", "Cheat Codes, Kris Kross Amsterdam", "Rihanna", "The Killers", "Vance Joy", "Ed Sheeran", "Kiiara", "Mark Ronson", "Eminem", "Snakehips", "Isaiah Firebrace", "Anne-Marie",
            "R. Kelly", "JAY-Z, Kanye West", "Jon Bellion", "ZAYN", "DJ Snake, Bipolar Sunshine", "Kanye West", "Luke Christopher", "Big Sean", "Chet Faker", "The Weeknd, Kendrick Lamar", "Vallis Alps", "Niall Horan",
            "Kanye West, Jamie Foxx", "Foster The People", "London Grammar", "Kendrick Lamar", "David Guetta", "TLC", "G-Eazy, Bebe Rexha", "Alok, Bruno Martini, Zeeba", "Cash Cash, Digital Farm Animals", "Outkast", "Flo Rida",
            "Chance the Rapper",
        ];
        let randomIndex = Math.floor(Math.random() * artists.length);
        return artists[randomIndex];
    }

    window.addEventListener("load", function() {
        const searchField = document.getElementById(searchFieldId);
        const searchButton = document.getElementById(searchButtonId);

        if (!searchField || !searchButton) {
            console.error(`Sökfält eller knapp hittades inte. Fält: ${searchFieldId}, Knapp: ${searchButtonId}`);
            return;
        }

        let storedStartTime = parseFloat(localStorage.getItem("startTime") || "0");
        let storedSearchTerm = localStorage.getItem("searchterm");

        if (storedStartTime) {
            let endTime = performance.timeOrigin + performance.now();
            let elapsedTime = Math.round(endTime - storedStartTime);

            elapsedData.push({
                timestamp: Date.now(),
                elapsedTime: elapsedTime,
                searchTerm: storedSearchTerm
            });

            localStorage.setItem('elapsedTimes', JSON.stringify(elapsedData));
            count++;
            localStorage.setItem('count', count);
        }

        if (count < nrWords) {
            let searchTerm = getRandomArtist();
            searchTerms.push(searchTerm);
            localStorage.setItem('searchTerms', JSON.stringify(searchTerms));

            searchField.value = searchTerm;

            let startTime = performance.timeOrigin + performance.now();
            localStorage.setItem('startTime', startTime);
            localStorage.setItem('searchterm', searchTerm);

            setTimeout(() => searchButton.click(), );
        } else {
            console.log("Alla sökningar klara.");
            downloadCSV();
            localStorage.clear();
        }
    });

    function downloadCSV() {
        if (elapsedData.length === 0) return;

        let csvContent = "Millisekunder,Sökterm,Timestamp\n";
        elapsedData.forEach(entry => {
            csvContent += `${entry.elapsedTime},${entry.searchTerm},${entry.timestamp}\n`;
        });

        let blob = new Blob([csvContent], { type: 'text/csv' });
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `elapsed_time_search_${elapsedData.length}.csv`;
        document.body.appendChild(link);
        link.click();
        console.log("CSV nedladdad.");
    }

})();
