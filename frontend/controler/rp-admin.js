const etageNumber = document.querySelector("#etage-number");
const etageUp = document.querySelector("#etage-up");
const etageDown = document.querySelector("#etage-down");

let etage = 0;

let data = [];

etageDown.addEventListener("click", function (e) {
  if (!(etage < -99)) {
    etage -= 1;
  }
  placeEtageNumber();
  RenderAll();
});
etageUp.addEventListener("click", function (e) {
  if (!(etage > 99)) {
    etage += 1;
  }
  placeEtageNumber();
  RenderAll();
});
function placeEtageNumber() {
  etageNumber.innerHTML = etage;
  if (etage === 0) {
    etageNumber.innerHTML = "EG";
  }
}

// THIS IS TO DISPLAY ALL THE ROOMS AND PARKINGSPACES

const tabelPlaces = document.querySelector("#tabel-places");

const canvas = document.querySelector("#canvas");
const ctx = canvas.getContext("2d");

const CANVAS_WIDTH = (canvas.width = 900);
const CANVAS_HEIGHT = (canvas.height = 900);

function RenderAll() {
  ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
  tabelPlaces.innerHTML = `
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Löschen</th>
    </tr>
  `;

  for (let i = 0; i < CANVAS_WIDTH; i += 10) {
    ctx.fillRect(i, 0, 1, 10);
  }
  for (let i = 0; i < CANVAS_HEIGHT; i += 10) {
    ctx.fillRect(0, i, 10, 1);
  }
  ctx.fillRect(20, CANVAS_HEIGHT - 20, 50, 5);
  ctx.fillText("50Pixel = 2 Meter", 20, CANVAS_HEIGHT - 25);

  data.forEach((Element, index) => {
    if (Element == undefined) {
      MessageUI("Error", "Data Is Corupted");
    } else if (Element.position == undefined) {
      MessageUI("Error", "Data Is Incomplete and unable to be displayed");
    } else {
      let type = "";
      if (Element.type == "R") {
        type = "Raum";
      } else if (Element.type == "P") {
        type = "Parkplatz";
      } else {
        type = "UnIdentified Thing";
      }
      if (JSON.parse(Element.position).etage == etage) {
        ctx.fillStyle = "#0A0A32";

        const x = parseInt(JSON.parse(Element.position).x);
        const y = parseInt(JSON.parse(Element.position).y);
        const width = parseInt(JSON.parse(Element.position).width);
        const height = parseInt(JSON.parse(Element.position).height);

        ctx.fillRect(x, y, width, height);

        // WHITE
        ctx.fillStyle = "#FFFFFF";

        ctx.fillText(
          index + ": " + Element.name + ", " + type,
          x +
            width / 2 -
            (index + " :" + Element.name + ", " + type).length * 2,
          y + height / 2
        );
      }
      tabelPlaces.innerHTML += `
                  <tr>
                      <td>${Element.name}</td>
                      <td>${type}</td>
                      <td><button onclick="placeDelete('${Element.name}')">Löschen</button></td>
                  </tr>
                  `;
    }
  });

  ctx.fillStyle = "#0000AA";

  ctx.fillRect(
    positionX.value,
    positionY.value,
    positionWidth.value,
    positionLenght.value
  );
}

/**
 *
 * @param {*} name
 */
function placeDelete(name) {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    if (
      request.status == 400 ||
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten konnten nicht gelöscht werden: " +
          JSON.parse(request.responseText).error
      );
    } else {
      MessageUI("Succes", "Erfolgreich Gelöscht");
      request();
    }
  };

  let request = new XMLHttpRequest();
  request.open("DELETE", "../../API/V1/Place/" + name);
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

function request() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    if (
      request.status == 400 ||
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten Konnten Nicht Geholt werden oder Es Gibt keine"
      );
    } else if (request.status == 500 || request.status == 501) {
      MessageUI(
        "Server Error: " + request.statusText,
        "Der Server Hat einen Kritischen Fehler erliten: " +
          request.responseText
      );
    } else {
      try {
        data = JSON.parse(request.responseText);
        RenderAll();
      } catch (e) {
        MessageUI("Beschädigte daten", "Daten Funktionieren nicht: " + e);
      }
    }
  };

  let request = new XMLHttpRequest();
  request.open("GET", "../../../../API/V1/Places");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

request();

// This function makes that every 3 Seconds New Data Will get requested
setInterval(request, 3000);

document.querySelector("#confirm").addEventListener("click", function (e) {
  newObject();
});

const positionX = document.querySelector("#position-x");
const positionY = document.querySelector("#position-y");
const positionWidth = document.querySelector("#position-width");
const positionLenght = document.querySelector("#position-lenght");

const objectName = document.querySelector("#object-name");

const radioP = document.querySelector("#radio-p");

positionX.value = 25;
positionY.value = 25;
positionWidth.value = 25;
positionLenght.value = 25;
objectName.value = "Thing";
radioP.checked = true;

function newObject() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    if (
      request.status == 400 ||
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten Konnten Nicht Gespeichert werden: " +
          JSON.parse(request.responseText).error
      );
    } else {
      data = JSON.parse(request.responseText);
      RenderAll();
      MessageUI("Neues Objekt Erstelt", "");
    }
  };

  let request = new XMLHttpRequest();
  request.open("POST", "../../../../API/V1/Place");
  request.onreadystatechange = onRequstUpdate;

  const position = {
    x: positionX.value,
    y: positionY.value,
    width: positionWidth.value,
    height: positionLenght.value,
    etage: etage,
  };

  let radio = "";
  if (radioP.checked === true) {
    radio = "P";
  } else {
    radio = "R";
  }

  const requestArray = {
    name: objectName.value,
    position: JSON.stringify(position),
    type: radio,
  };

  request.send(JSON.stringify(requestArray));
}
