package jsonparser;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import javafx.fxml.FXML;
import javafx.scene.control.TextField;

import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Scanner;

public class JSONParser {
    public String path;

    public JSONParser(String path) {
        this.path = path;
    }

    public Config readJSON() throws IOException {
        FileReader reader = new FileReader(path);
        Scanner scanner = new Scanner(reader);
        StringBuilder builder = new StringBuilder();

        while (scanner.hasNextLine()) {
            builder.append(scanner.nextLine());
        }
        reader.close();

        String jsonStr = builder.toString();

        Gson gson = new GsonBuilder().create();

        return gson.fromJson(jsonStr, Config.class);
    }

    public void overwriteJSON(Config config) throws IOException {
        Gson gson = new GsonBuilder().create();
        String jsonStr = gson.toJson(config);

        FileWriter writer = new FileWriter(path);
        writer.write(jsonStr);
        writer.close();
    }
}
