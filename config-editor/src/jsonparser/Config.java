package jsonparser;

public class Config {
    public double profit;
    public double lower_edge;
    public double upper_edge;
    public int popularity_7d;
    public String api_key;

    public Config(double profit,
                  double lower_edge,
                  double upper_edge,
                  int popularity_7d,
                  String api_key) {
        this.profit = profit;
        this.lower_edge = lower_edge;
        this.upper_edge = upper_edge;
        this.popularity_7d = popularity_7d;
        this.api_key = api_key;
    }
}
