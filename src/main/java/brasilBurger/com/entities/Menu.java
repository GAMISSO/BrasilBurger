package brasilBurger.com.entities;

import com.mysql.cj.jdbc.Blob;
import lombok.*;

import java.time.LocalDate;
import java.util.List;

@Getter
@Setter
@ToString
@NoArgsConstructor
@AllArgsConstructor

public class Menu {
    private int id;
    private String nom;
    private int burgerId;
    private Burger burger;
    private List<Complement> complements;
    private int prixTotal;
    private LocalDate date;
    private String image;
}
