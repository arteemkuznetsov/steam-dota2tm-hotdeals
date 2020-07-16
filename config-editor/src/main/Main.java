package main;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.stage.Stage;

public class Main extends Application {
    @Override
    public void start(Stage primaryStage) throws Exception {
        Parent root = FXMLLoader.load(getClass().getResource("main.fxml"));
        primaryStage.setTitle("Config Editor");
        primaryStage.setScene(new Scene(root, 640, 480));
        primaryStage.show();
        Controller.stage = primaryStage;
    }

    public static void main(String[] args) { launch(args); }
}
