const dateFrom = document.querySelector("#date-from");
const timeFrom = document.querySelector("#time-from");
const dateTo = document.querySelector("#date-to");
const timeTo = document.querySelector("#time-to");

const place = document.querySelector("#place");
const description = document.querySelector("#description");

const confirmT = document.querySelector("#confirm");
const cancel = document.querySelector("#cancel");

const host = document.querySelector("#host");

const placeId = location.hash.substring(1);

confirmT.addEventListener("click", function (e) {
  requestPost();
});

cancel.addEventListener("click", function (e) {
  window.location = "../../index.html";
});

/**
 * this is the button to send data to the server
 */
function requestPost() {
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
        "Error: " + request.statusText,
        "Daten Konnten Nicht Gespeichert werden oder Es Gibt keine: " +
          JSON.parse(request.responseText).error
      );
    } else {
      MessageUI("Erfolg", "Reservierung wurde erfolgreich Verändert");
    }
  };
  let request = new XMLHttpRequest();
  request.open("PUT", "../../../../API/V1/Reservation/" + placeId);
  request.onreadystatechange = onRequstUpdate;

  let hostName;

  const selectThing = document.querySelector("#select");

  if (selectThing !== undefined && selectThing !== null) {
    // Source: https://stackoverflow.com/questions/1085801/get-selected-value-in-dropdown-list-using-javascript
    hostName = selectThing.options[selectThing.selectedIndex].text;
  } else {
    hostName = userName;
  }

  const requestArray = {
    host: hostName,
    from_date: dateFrom.value + " " + timeFrom.value + ":00",
    to_date: dateTo.value + " " + timeTo.value + ":00",
    place_name: place.value,
    description: description.value,
  };
  request.send(JSON.stringify(requestArray));
}

/**
 * "from_date": "2023-03-02 14:43:00",
 * "to_date": "2023-03-02 14:55:00",
 * "place_name": "Rubin",
 * "host": "mouayad",
 * "descriptopm": ""
 */

let userName;
let userType;

/**
 * here will be the validation of the result
 * @returns if the server didn't responde corectly
 */
const onRequstUpdateWhoami = function () {
  if (requestWhoami.readyState < 4) {
    return;
  }
  const response = JSON.parse(requestWhoami.responseText);
  userName = response.name;
  userType = response.type;

  if (userType == "A" || userType == "S") {
    const onRequstUpdateSelectThing = function () {
      if (requestSelectThing.readyState < 4) {
        return;
      }
      let retunedData = [];
      retunedData = JSON.parse(requestSelectThing.responseText);

      const selectThing = document.createElement("select");

      selectThing.id = "select";

      /** i dont know why the .lenght does not work but this works somehow
       * Source: https://stackoverflow.com/questions/5317298/find-length-size-of-an-array-in-javascript
       */
      function count(array) {
        let c = 0;
        for (i in array) // in returns key, not object
          if (array[i] != undefined) c++;

        return c;
      }

      for (let i = 0; i < count(retunedData); i++) {
        const newSelectThing = document.createElement("option");

        newSelectThing.innerText = retunedData[i].name;
        newSelectThing.value = retunedData[i].name;

        selectThing.appendChild(newSelectThing);
      }

      host.appendChild(selectThing);
    };
    const requestSelectThing = new XMLHttpRequest();
    requestSelectThing.open("GET", "../../../../API/V1/Users");
    requestSelectThing.onreadystatechange = onRequstUpdateSelectThing;
    requestSelectThing.send();
  } else {
    host.innerText = userName;
  }
};

const requestWhoami = new XMLHttpRequest();
requestWhoami.open("GET", "../../../../API/V1/WhoAmI");
requestWhoami.onreadystatechange = onRequstUpdateWhoami;
requestWhoami.send();

const onRequstUpdateThing = function () {
  if (requestGetPlace.readyState < 4) {
    return;
  }
  if (
    requestGetPlace.status == 400 ||
    requestGetPlace.status == 401 ||
    requestGetPlace.status == 404 ||
    requestGetPlace.status == 403
  ) {
    MessageUI(
      "Error: " + requestGetPlace.statusText,
      "Daten Konnten Nicht Gespeichert werden oder Es Gibt keine: " +
        JSON.parse(requestGetPlace.responseText).error
    );
  } else {
    let data = JSON.parse(requestGetPlace.responseText);

    dateFrom.value = data.from_date.split(" ")[0];
    timeFrom.value = data.from_date.split(" ")[1];
    dateTo.value = data.to_date.split(" ")[0];
    timeTo.value = data.to_date.split(" ")[1];
    place.value = data.place_name;
    description.value = data.description;
  }
};

const requestGetPlace = new XMLHttpRequest();
requestGetPlace.open("GET", "../../../../API/V1/Reservation/" + placeId);
requestGetPlace.onreadystatechange = onRequstUpdateThing;
requestGetPlace.send();
