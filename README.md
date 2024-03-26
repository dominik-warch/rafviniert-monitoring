# RAFVINIERT Monitoring
## Introduction
RAFVINIERT Monitoring is a part of a larger project aimed at improving the living conditions of seniors in rural areas by providing a platform for local authorities to upload and visualize data. The project focuses on developing architectures and implementation strategies for small-scale monitoring of areas facing supply shortages, especially affecting seniors. This project is being conducted at Hochschule Mainz and is funded by the Carl-Zeiss-Stiftung.

Our team at i3mainz, in collaboration with rural districts and communities, is developing tools and strategies for the supply and monitoring of these areas. This includes creating tools for small-scale monitoring within municipal geodata infrastructures and developing accessibility calculations for supply facilities with a focus on senior citizens.

## Features
* **Data Upload Capability:** Users can upload various data like Citizen Registration Records in CSV or Excel.
* **Data Processing:** The platform automatically geocodes the uploaded data and calculates various demographic indicators like median age, total dependency ratio, or remanence buildings.
* **Data Visualization:** Users can visualize these indicators on an interactive web map, providing a clear and accessible overview of the data.

## Technologies Used
* Laravel & Laravel Livewire
* Inertia & React
* Deck.GL & MapLibre
* pygeoapi

## Requirements
To run this application, you'll need:

* Docker: For creating, deploying, and running applications using containers.
* Docker Compose: For defining and running multi-container Docker applications.

## Installation and Setup
To set up the application:

1. **Clone the Repository:** Use `git clone https://github.com/dominik-warch/rafviniert-monitoring.git` to clone the project to your local machine.
2. **Environment Setup:** Rename `.env.example` to `.env.` Fill in the necessary environmental variables, especially those related to the database and other services.
3. **Start the Application:** Use the command `docker compose -f docker-compose-test.yml up` in the project's root directory to build and start the application.
4. **Run Migrations:** Use the command `docker compose exec -it php ./artisan migrate` in the project's root directory to migrate the database.

## Deployment
The deployment process mirrors the installation and setup. Ensure that your production environment is equipped with Docker and Docker Compose. For more advanced deployment strategies, consider container orchestration tools like Kubernetes or Docker Swarm.

## Authors and Acknowledgements
* **Main Author:** Dominik Warch
* **Collaborators:** Mariyan Stamenov
* **Institutional Support:** This project is a part of research at i3mainz and is financed by the Carl-Zeiss-Stiftung.

## License
RAFVINIERT Monitoring is open-source software licensed under the MIT License.

## Contact Information
For queries or contributions, contact Dominik Warch at dominik.warch@hs-mainz.de.
