const ownReservations = document.querySelector("#own-reservations");

function requestingOwnReservations() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    const response = JSON.parse(request.responseText);

    const userName = response.name;

    const renderTheReservation = function () {
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
        const dataReservated = JSON.parse(requestReservation.responseText);
        ownReservations.innerHTML = `
        <tr>
            <th>Raum Name</th>
            <th>Beschreibung</th>
        </tr>
        `;
        dataReservated.forEach(element => {
            if(element.host == userName) {
                const NEW_ROW = document.createElement("tr");
                NEW_ROW.className = "white";

                const ROOM_NAME = document.createElement("td");
                const DESCRIPTION = document.createElement("td");

                ROOM_NAME.innerText = element.place_name;

                DESCRIPTION.innerHTML = `
                From: ${element.from_date}
                To: ${element.to_date}
                <br>
                <button onclick="reservationDelete(${element.id})">Löschen</button>
                <a href="reservation-edit.html#${element.id}">Editieren</a>
                `;

                NEW_ROW.appendChild(ROOM_NAME);
                NEW_ROW.appendChild(DESCRIPTION);

                ownReservations.appendChild(NEW_ROW);
            }
        });
      }
    };

    const requestReservation = new XMLHttpRequest();
    requestReservation.open("GET", "../../../../API/V1/Reservations");
    requestReservation.onreadystatechange = renderTheReservation;
    requestReservation.send();
  };

  const request = new XMLHttpRequest();
  request.open("GET", "../../../../API/V1/WhoAmI");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

// every 30 seconds this function will be called
setInterval(requestingOwnReservations, 30000);

requestingOwnReservations();


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