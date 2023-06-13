const tabelReservations = document.querySelector("#tabel-reservations");

let data = [];

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
      request.status == 401 ||
      request.status == 404 ||
      request.status == 403
    ) {
      MessageUI("Error", "Daten Konnten Nicht Geholt werden");
    } else {
      data = JSON.parse(request.responseText);
      RenderAll();
    }
  };

  let request = new XMLHttpRequest();
  request.open("GET", "../../../../API/V1/Reservations");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

function RenderAll() {
  tabelReservations.innerHTML = `
        <tr>
            <th>Name</th>
            <th>Datum</th>
        </tr>
        `;

  data.forEach((Element) => {
    if (Element !== null) {
      tabelReservations.innerHTML += `
                <tr>
                    <td>${Element.place_name}</td>
                    <td>${
                      "Von: " + Element.from_date + " Bis: " + Element.to_date
                    }</td>
                </tr>
            `;
    } else {
      MessageUI("Error", "Beschädigte Daten Wurden Erhalten");
    }
  });
}

request();

// this function is played every 60 Seconds
setInterval(request, 60000);
