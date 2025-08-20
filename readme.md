<a name="readme-top"></a>


<!-- PROJECT LOGO -->
<br />
<div align="center">

<h3 align="center">Home Assistant - Bus Stop Times</h3>

  <p align="center">
    List of bus arrivals at your stop in a Home Assistant card.
    <br />
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#prerequisites">Prerequisites</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
    <li><a href="#acknowledgments">Acknowledgments</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

I wanted a project that could show how Home Assistant could be used for more than simply turning on and off the lights. I am slowly working up to having a dashboard display on an old Android tablet and I decided that what would be useful would be a list of the next bus arrivals at our nearest stop. 

This is based around Reading Buses but it will work with any bus company that uses the [r2p](https://www.r2p.com/) service which includes both Oxford and Plymouth to my knowledge at the time of writing.

Read more about the project [here](https://www.spokenlikeageek.com/2025/09/01/home-assistant-bus-stop-times/).

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/08/SCR-20250820-kteq.png)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



### Built With

* [PHP](https://php.net)
* [Reading Buses api](https://reading-opendata.r2p.com/)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

Getting up and running is very straightforward:

1. download the code/clone the repository    
4. follow the installation instructions below.


You can read more about how it all works in [these blog posts](https://www.spokenlikeageek.com/tag/bus-stop-times/).

### Prerequisites

Requirements are very simple, it requires the following:

1. PHP (I tested on v8.3.16)
2. An account with [r2p](https://www.r2p.com/) for your relevant bus company
3. [Home Assistant](https://home-assistant.io)

### Installation

You must install the dependencies, create some required files and set the appropriate permissions. This is what I did but you may need to adjust depending on your flavour of OS:

1. ```git clone https://github.com/williamsdb/Home-Assistant---Bus-Stop-Times```
2. ```cd Home-Assistant---Bus-Stop-Times\src```
9. ```sudo mv config_dummy.php config.php```

Edit the config.php file to add your:

* api key
* bus stop location
* filter the lines you are interested in (a comma separated array).

To find your stop go to ```https://<your domain>/stops.php``` and copy the location code.

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/08/SCR-20250820-jfnu-scaled.png)


### Integration into Home Assistant

In order to use the script with Home Assistant you will need to tell HA about the feed. You do this by adding a new entry into your configuration.yml file as follows:

```yaml
rest:
  - scan_interval: 60  # Update every minute (buses update frequently)
    resource: https://<your domain>/arrivals.php
    sensor:
      # Sensor to show all upcoming buses
      - name: All Upcoming Buses
        unique_id: all_upcoming_buses
        value_template: "{{ value_json.buses | length }} buses scheduled"
        icon: mdi:bus-multiple
        json_attributes:
          - buses
```

Remember to change the above to have your domain name.

#### Basic card

For a very simple card with no formatting you can add the following markdown card to your dashboard:

```yaml
type: markdown
title: Bus Schedule
content: |
  {% set buses = state_attr('sensor.all_upcoming_buses', 'buses') %}
  {% for bus in buses %}
  {{ bus.time }}  {{ bus.due_in_str }} ({{ bus.type }}) 
  ---
  {% endfor %}
```

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/08/SCR-20250820-jxjk.png)

#### Formatted card

This uses an HTML table for better formatting:

```yaml
type: markdown
title: Bus Schedule
content: |
  <table width="100%">
    <tr class="header">
      <th>Time</th>
      <th>Due In</th>
      <th>Type</th>
    </tr>
    {% set buses = state_attr('sensor.all_upcoming_buses', 'buses') %}
    {% for bus in buses %}
    <tr class="data {% if bus.type|lower == 'expected' %}expected{% endif %}">
      <td align="center">{{ bus.time }}</td>
      <td align="center">{{ bus.due_in_str }}{% if bus.due_in > 1 %}s{% endif %}</td>
      <td align="center">{{ bus.type }}</td>
    </tr>
    {% endfor %}
  </table>
```

![](https://www.spokenlikeageek.com/wp-content/uploads/2025/08/SCR-20250820-kuws.png)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- USAGE EXAMPLES -->
## Usage

_For more information, please refer to the [these blog posts](https://www.spokenlikeageek.com/tag/bus-stop-times/)_

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ROADMAP -->
## Known Issues

- None

See the [open issues](https://github.com/williamsdb/Home-Assistant---Bus-Stop-Times/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- LICENSE -->
## License

Distributed under the GNU General Public License v3.0. See `LICENSE` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

X - [@spokenlikeageek](https://x.com/spokenlikeageek) 

Bluesky - [@spokenlikeageek.com](https://bsky.app/profile/spokenlikeageek.com)

Mastodon - [@spokenlikeageek](https://techhub.social/@spokenlikeageek)

Website - [https://spokenlikeageek.com](https://www.spokenlikeageek.com/tag/bus-stop-times/)


Project link - [Github](https://github.com/williamsdb/Home-Assistant---Bus-Stop-Times)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- ACKNOWLEDGMENTS -->
## Acknowledgments

* None

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/github_username/repo_name.svg?style=for-the-badge
[contributors-url]: https://github.com/github_username/repo_name/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/github_username/repo_name.svg?style=for-the-badge
[forks-url]: https://github.com/github_username/repo_name/network/members
[stars-shield]: https://img.shields.io/github/stars/github_username/repo_name.svg?style=for-the-badge
[stars-url]: https://github.com/github_username/repo_name/stargazers
[issues-shield]: https://img.shields.io/github/issues/github_username/repo_name.svg?style=for-the-badge
[issues-url]: https://github.com/github_username/repo_name/issues
[license-shield]: https://img.shields.io/github/license/github_username/repo_name.svg?style=for-the-badge
[license-url]: https://github.com/github_username/repo_name/blob/master/LICENSE.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/linkedin_username
[product-screenshot]: images/screenshot.png
[Next.js]: https://img.shields.io/badge/next.js-000000?style=for-the-badge&logo=nextdotjs&logoColor=white
[Next-url]: https://nextjs.org/
[React.js]: https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB
[React-url]: https://reactjs.org/
[Vue.js]: https://img.shields.io/badge/Vue.js-35495E?style=for-the-badge&logo=vuedotjs&logoColor=4FC08D
[Vue-url]: https://vuejs.org/
[Angular.io]: https://img.shields.io/badge/Angular-DD0031?style=for-the-badge&logo=angular&logoColor=white
[Angular-url]: https://angular.io/
[Svelte.dev]: https://img.shields.io/badge/Svelte-4A4A55?style=for-the-badge&logo=svelte&logoColor=FF3E00
[Svelte-url]: https://svelte.dev/
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
