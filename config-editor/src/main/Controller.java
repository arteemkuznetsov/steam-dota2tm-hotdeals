package main;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.TextField;
import javafx.stage.FileChooser;
import javafx.stage.Stage;
import jsonparser.Config;
import jsonparser.JSONParser;

import java.io.File;

public class Controller {
    static Stage stage;
    static JSONParser parser;
    static Config config;
    @FXML
    TextField profitInput;
    @FXML
    TextField lowerEdgeInput;
    @FXML
    TextField upperEdgeInput;
    @FXML
    TextField popularity7dInput;
    @FXML
    TextField apiKeyInput;
    @FXML
    Button saveButton;

    @FXML
    void chooseFile() {
        FileChooser fileChooser = new FileChooser();
        FileChooser.ExtensionFilter filter = new FileChooser.ExtensionFilter("JSON files (*.json)", "*.json");
        fileChooser.getExtensionFilters().add(filter);
        File file = fileChooser.showOpenDialog(stage);
        try {
            String path = file.getAbsolutePath();
            parser = new JSONParser(path);
            config = parser.readJSON();

            profitInput.setText(String.valueOf(config.profit));
            lowerEdgeInput.setText(String.valueOf(config.lower_edge));
            upperEdgeInput.setText(String.valueOf(config.upper_edge));
            popularity7dInput.setText(String.valueOf(config.popularity_7d));
            apiKeyInput.setText(String.valueOf(config.api_key));

            saveButton.setDisable(false);
        }
        catch (Exception ignored) {}
    }

    @FXML
    void saveFile() throws Exception {
        config = new Config(
                Double.parseDouble(profitInput.getText()),
                Double.parseDouble(lowerEdgeInput.getText()),
                Double.parseDouble(upperEdgeInput.getText()),
                Integer.parseInt(popularity7dInput.getText()),
                apiKeyInput.getText()
        );
        parser.overwriteJSON(config);
    }
}
