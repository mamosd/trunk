<?php
?>
<style>
    .listing td {
        text-transform: uppercase;
        font-weight: bold;
        padding: 5px !important;
        text-align: center;
    }
    .amended {
        color: black !important;
        background-color: yellow !important;
        text-shadow: none !important;
    }
    .cancelled {
        color: white !important;
        background-color: red !important;
        text-shadow: none !important;
    }
    .confirmed {
        color: white !important;
        background-color: green !important;
        text-shadow: none !important;
    }
    .data-completed {
        color: white !important;
        background-color: green !important;
        text-shadow: none !important;
    }
    .late-advice {
        color: white !important;
        background-color: blue !important;
        text-shadow: none !important;
    }
    .newly-added {
        color: white !important;
        background-color: black !important;
        text-shadow: none !important;
    }
    .same-day {
        color: black !important;
        background-color: orange !important;
        text-shadow: none !important;
    }
    .booked {
        color: white !important;
        background-color: green !important;
        text-shadow: none !important;
    }
</style>

<table class="listing fluid" caption="polestar_status (8 rows)">
      <thead>
        <tr>
          <th class="col0">Id</th>
          <th class="col1">Name</th>
          <th class="col2">Code</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="amended">A</td>
          <td class="amended">Amended</td>
          <td class="amended">amended</td>
        </tr>
        <tr>
          <td class="booked">B</td>
          <td class="booked">Booked</td>
          <td class="booked">booked</td>
        </tr>
        <tr>
          <td class="cancelled">CA</td>
          <td class="cancelled">Cancelled</td>
          <td class="cancelled">cancelled</td>
        </tr>
        <tr>
          <td class="confirmed">CO</td>
          <td class="confirmed">Confirmed</td>
          <td class="confirmed">confirmed</td>
        </tr>
        <tr>
          <td class="data-completed">DC</td>
          <td class="data-completed">Data Completed</td>
          <td class="data-completed">data-completed</td>
        </tr>
        <tr>
          <td class="late-advice">LA</td>
          <td class="late-advice">Late Advice</td>
          <td class="late-advice">late-advice</td>
        </tr>
        <tr>
          <td class="newly-added">NA</td>
          <td class="newly-added">Newly Added</td>
          <td class="newly-added">newly-added</td>
        </tr>
        <tr>
          <td class="same-day">SD</td>
          <td class="same-day">Same Day Advice</td>
          <td class="same-day">same-day</td>
        </tr>
      </tbody>
    </table>