<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Packages - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/service-packages.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo">
      <h2>AUTONEXUS</h2>
      <p>VEHICLE SERVICE</p>
    </div>
 
  <ul class="menu">
  <li><a href="/autonexus/receptionist/dashboard">Dashboard</a></li>
  <li><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li class="active"><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>

  </div>

  <div class="main">
    <div class="header">
      <h2>Service Packages</h2>
    </div>
    


    <div class="packages">
      <table>
        <thead>
          <tr>
            <th>Package Name</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price</th>
          </tr>
        </thead>
        <tbody>
          
           <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>Oil change, filter replacement, basic inspection ...
                    <div class="Service-item hidden">
                      <h4>Service Item</h4>
                      <ul class="dropdown-list">
                          <li>Item 1</li>
                          <li>Item 2</li>
                          <li>Item 3</li>
                      </ul></div>
            </td>
            <td>120 min</td>
            <td class = "price">$149.99</td>
            </tr>

            <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>Oil change, filter replacement, basic inspection ...
                    <div class="Service-item hidden">
                      <h4>Service Item</h4>
                      <ul class="dropdown-list">
                          <li>Item 1</li>
                          <li>Item 2</li>
                          <li>Item 3</li>
                      </ul></div>
            </td>
            <td>120 min</td>
            <td class="price">$349.99</td>
            </tr>

            <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>Oil change, filter replacement, basic inspection ...
                    <div class="Service-item hidden">
                      <h4>Service Item</h4>
                      <ul class="dropdown-list">
                          <li>Item 1</li>
                          <li>Item 2</li>
                          <li>Item 3</li>
                      </ul></div>
            </td>
            <td>120 min</td>
            <td class="price">$149.99</td>
            </tr>

            <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Brake Service</td>
            <td>Complete brake system
                    <div class="Service-item hidden">
                      <h4>Service Item</h4>
                      <ul class="dropdown-list">
                          <li>Item 1</li>
                          <li>Item 2</li>
                          <li>Item 3</li>
                      </ul></div>
            </td>
            <td>120 min</td>
            <td class="price">$199.99</td>
            </tr>


      

         <!--<tr class="expandable">
            <td>Basic Service</td>
            <td>Oil change, filter replacement, and basic inspection</td>
            <td>60 min</td>
            <td>$79.99</td>
            <td>
            <div class="dropdown">
                <button id="toggleButton" class="toggle-button">▼</button>
                <ul id="dropdownList" class="dropdown-list hidden">
                    <li>Item 1</li>
                    <li>Item 2</li>
                    <li>Item 3</li>
                </ul></div></td>
             <td class="actions"><a href="editService.html">✎</a> 🗑</td>

           <tr class="clickable-row" onclick="toggleDropdown(this)">
            <td>Full Service</td>
            <td>Oil change, filter replacement, basic inspection ...
                    <div class="Service-item">
                      <h4>Service Item</h4>
                      <ul id="dropdownList" class="dropdown-list hidden">
                          <li>Item 1</li>
                          <li>Item 2</li>
                          <li>Item 3</li>
                      </ul></div>
            </td>
            <td>120 min</td>
            <td>$149.99</td>
            <td class="actions"><a href="editService.html">✎</a> 🗑</td>
            </tr>
            
           
             
        
          <tr>
            <td>Brake Service</td>
            
            <td>
            <div class="dropdown">
                <button id="toggleButton" class="toggle-button">▼</button>
                <ul id="dropdownList" class="dropdown-list hidden">
                    <li>Item 1</li>
                    <li>Item 2</li>
                    <li>Item 3</li>
                </ul></div></td>
            <td class="actions"><a href="editService.html">✎</a> 🗑</td>
          </tr>
          <tr>
            <td></td>
            <td>Complete brake system</td>
            <td>120 min</td>
            <td>$199.99</td>
            <td>
            <div class="dropdown">
                <button id="toggleButton" class="toggle-button">▼</button>
                <ul id="dropdownList" class="dropdown-list hidden">
                    <li>Item 1</li>
                    <li>Item 2</li>
                    <li>Item 3</li>
                </ul></div></td>
             <td class="actions"><a href="editService.html">✎</a> 🗑</td>
          </tr>--> 
        </tbody>
      </table>
    </div>
  </div>

    <script src="<?= BASE_URL ?>/public/assets/r_js/Services.js"></script>


</body>
</html>
