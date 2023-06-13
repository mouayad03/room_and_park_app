const etageNumber = document.querySelector("#etage-number");
const etageUp = document.querySelector("#etage-up");
const etageDown = document.querySelector("#etage-down");

let etage = 0;

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

// search function

const searchPlace = document.querySelector("#search-input");
const searchButton = document.querySelector("#search-button");

searchButton.addEventListener("click", function (e) {
  const searchValue = searchPlace.value;
  window.location = "rp.html#" + searchValue;
});

// THIS IS TO DISPLAY ALL THE ROOMS AND PARKINGSPACES

const tabelReservations = document.querySelector("#tabel-reservations");

const canvas = document.querySelector("#canvas");
const ctx = canvas.getContext("2d");

const CANVAS_WIDTH = (canvas.width = 900);
const CANVAS_HEIGHT = (canvas.height = 900);

function RenderAll() {
  tabelReservations.innerHTML = `
  <tr>
    <th>Name</th>
    <th>Besetztungen</th>
    <th>Reservieren</th>
  </tr>
    `;
  ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);
  ctx.fillStyle = "#000000";

  /**
   * This Algorith is rendering all Places and all the Reservations that ar Set to this place
   */
  dataPlaces.forEach((ElementPlace, index) => {
    if (ElementPlace == undefined) {
      MessageUI("Error", "Places Data Is Corupted");
    } else {
      let AllReservationsFromThisPlace = [];

      // Checks if there is a Reservation for this Place
      dataReservated.forEach((ElementReservation) => {
        if (ElementReservation == undefined) {
          MessageUI("Error", "Reservation Data Is Corupted");
        } else if (
          ElementReservation.from_date == undefined ||
          ElementReservation.to_date == undefined ||
          ElementReservation.host == undefined
        ) {
          MessageUI("Error", "Data Is Incomplete");
        } else {
          // Reservations For this place are added to array to list them later up
          if (ElementReservation.place_name == ElementPlace.name) {
            const reservation = {
              reservation_id: ElementReservation.id,
              from: ElementReservation.from_date,
              to: ElementReservation.to_date,
              host: ElementReservation.host,
              description: ElementReservation.description,
            };
            AllReservationsFromThisPlace.push(reservation);
          }
        }
      });

      // Checks If the type is a Room Or a Parkingspace
      let type = "";
      if (ElementPlace.type == "R") {
        type = "Raum";
      } else if (ElementPlace.type == "P") {
        type = "Parkplatz";
      } else {
        type = "UnIdentified Thing";
      }

      // When the UI is in the exact Etage it will Render it On Screen
      if (JSON.parse(ElementPlace.position).etage === etage) {
        const X = parseInt(JSON.parse(ElementPlace.position).x);
        const Y = parseInt(JSON.parse(ElementPlace.position).y);
        const WIDTH = parseInt(JSON.parse(ElementPlace.position).width);
        const HEIGHT = parseInt(JSON.parse(ElementPlace.position).height);

        ctx.fillStyle = "#000000";
        if (AllReservationsFromThisPlace.length < 1) {
          ctx.fillStyle = "#00AF00";
        } else {
          ctx.fillStyle = "#AF0000";
        }

        ctx.fillRect(X, Y, WIDTH, HEIGHT);

        ctx.fillStyle = "#FFFFFF";

        ctx.fillText(
          index + ": " + ElementPlace.name + ", " + type,
          X +
            WIDTH / 2 -
            (index + " :" + ElementPlace.name + ", " + type).length * 2,
          Y + HEIGHT / 2
        );
      }

      // New Row in the table of the html document
      const NEW_ROW = document.createElement("tr");

      const NAME = document.createElement("td");
      const CASTS = document.createElement("td");
      const RESERVE = document.createElement("td");

      NAME.innerText = index + ") " + ElementPlace.name;
      NAME.id = ElementPlace.name;

      // Lists all Reservations to the Casts field
      AllReservationsFromThisPlace.forEach((reservation) => {
        const NEW_RESERVATION = document.createElement("li");

        const DELETE_EDIT = document.createElement("p");
        const INFORMATION = document.createElement("p");

        NEW_RESERVATION.className = "white";

        DELETE_EDIT.innerHTML =
          "<button onclick='reservationDelete(" +
          reservation.reservation_id +
          ")'>Löschen</button>" +
          "<a href='reservation-edit.html#" +
          reservation.reservation_id +
          "'>Editieren</>";

        INFORMATION.innerHTML =
          "Von: <p class='white' id='" +
          reservation.from +
          "'>" +
          reservation.from +
          "</p> Bis: <p class='white' id='" +
          reservation.to +
          "'>" +
          reservation.to +
          "</p><br> Host/Reservierender: <p class='white' id='" +
          reservation.host +
          "'>" +
          reservation.host +
          "</p> Wegen:" +
          reservation.description;

        NEW_RESERVATION.appendChild(INFORMATION);
        NEW_RESERVATION.innerHTML += "<br>";
        NEW_RESERVATION.appendChild(DELETE_EDIT);

        CASTS.appendChild(NEW_RESERVATION);
      });

      RESERVE.innerHTML =
        "<a href='reservation.html#" +
        ElementPlace.name +
        "'>Reservation machen</>";

      NEW_ROW.appendChild(NAME);
      NEW_ROW.appendChild(CASTS);
      NEW_ROW.appendChild(RESERVE);

      tabelReservations.appendChild(NEW_ROW);
    }
  });
}

/**
 *
 * @param {*} id
 */
function reservationDelete(id) {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (requestPlace.readyState < 4) {
      return;
    }
    const response = JSON.parse(requestPlace.responseText);
    if (
      requestPlace.status == 401 ||
      requestPlace.status == 404 ||
      requestPlace.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten konnten nicht gelöscht werden: " + response.error
      );
    } else {
      MessageUI("Erfolg", "Eine Reservierung wurde erfolgreich Gelöscht");
      requestPlace();
    }
  };

  let requestPlace = new XMLHttpRequest();
  requestPlace.open("DELETE", "../../API/V1/Reservation/" + id);
  requestPlace.onreadystatechange = onRequstUpdate;
  requestPlace.send();
}

function requestPlace() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdatePlaces = function () {
    if (requestPlace.readyState < 4) {
      return;
    }
    if (
      requestPlace.status == 400 ||
      requestPlace.status == 401 ||
      requestPlace.status == 404 ||
      requestPlace.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten Konnten Nicht Geholt werden oder Es Gibt keine"
      );
    } else if (requestPlace.status == 500 || requestPlace.status == 501) {
      MessageUI(
        "Server Error: " + requestPlace.statusText,
        "Der Server Hat einen Kritischen Fehler erliten: " +
          requestPlace.responseText
      );
    } else {
      try {
        dataPlaces = JSON.parse(requestPlace.responseText);
        RenderAll();
      } catch (e) {
        MessageUI("Beschädigte daten", "Daten Funktionieren nicht: " + e);
      }
    }
  };

  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdateReservations = function () {
    if (requestReservation.readyState < 4) {
      return;
    }
    if (
      requestReservation.status == 400 ||
      requestReservation.status == 401 ||
      requestReservation.status == 404 ||
      requestReservation.status == 403
    ) {
      MessageUI(
        "Error",
        "Daten Konnten Nicht Geholt werden oder Es Gibt keine"
      );
    } else if (
      requestReservation.status == 500 ||
      requestReservation.status == 501
    ) {
      MessageUI(
        "Server Error: " + requestReservation.statusText,
        "Der Server Hat einen Kritischen Fehler erliten: " +
          requestReservation.responseText
      );
    } else {
      dataReservated = JSON.parse(requestReservation.responseText);
      RenderAll();
    }
  };

  let requestPlace = new XMLHttpRequest();
  requestPlace.open("GET", "../../../../API/V1/Places");
  requestPlace.onreadystatechange = onRequstUpdatePlaces;
  requestPlace.send();

  let requestReservation = new XMLHttpRequest();
  requestReservation.open("GET", "../../../../API/V1/Reservations");
  requestReservation.onreadystatechange = onRequstUpdateReservations;
  requestReservation.send();
}

let dataPlaces = []; // Alle The Places that can be Reserved
let dataReservated = []; // All Reservations That Are Set.

// this function is played every 30 Seconds
setInterval(requestPlace, 30000);

requestPlace();
