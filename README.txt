# MP Pets Importer  

MP Pets Importer is a WordPress plugin that integrates with the **Petpoint API** to fetch and manage pet data. This plugin automates the creation of custom post types for **Dogs** and **Cats**, storing the fetched data in their respective post types.  

## Features  
- **Petpoint API Integration**: Seamlessly connect to the Petpoint API to import pet data.  
- **Custom Post Types**: Automatically creates custom post types for **Dogs** and **Cats** to organise and manage pet information.  
- **Automated Data Storage**: Fetched pet data is stored directly in the WordPress database under the respective custom post types.  
- **Extensible**: Built with flexibility in mind, allowing developers to extend its functionality.  

## Installation  
1. **Download the Plugin**: Clone or download the repository from GitHub.  
2. **Upload to WordPress**: Upload the plugin folder to the `wp-content/plugins` directory of your WordPress installation.  
3. **Activate the Plugin**: Go to the WordPress Admin Dashboard, navigate to **Plugins**, and activate **MP Pets Importer**.  
4. **API Setup**: Configure the Petpoint API settings in the plugin settings page (if applicable).  

## Usage  
1. **Custom Post Types**:  
   - The plugin automatically creates two custom post types:  
     - `Dog`: To store data for dogs.  
     - `Cat`: To store data for cats.  
2. **Import Data**:  
   - Use the API integration to fetch pet data and save it to the appropriate post type.  

## Shortcodes (If Applicable)  
- Display fetched pets on the frontend using shortcodes (e.g., `[mp_pet_list type="dog"]`).  
- Filter by categories or custom fields (details in the plugin settings).  

## Contributing  
Contributions are welcome! If you'd like to report a bug, suggest a feature, or contribute code:  
1. Fork the repository.  
2. Create a new branch (`feature/your-feature` or `bugfix/your-bug`).  
3. Commit your changes and push to your fork.  
4. Submit a pull request.  

## License  
This project is licensed under the MIT License. See the `LICENSE` file for details.  

## Author  
**Mayank Pandya**  
[Website](https://www.mayankpandya.com) | [LinkedIn](https://www.linkedin.com/in/mayank-pandya)  

---  

Feel free to customise this readme further to include more details about your plugin's functionality or future updates.
