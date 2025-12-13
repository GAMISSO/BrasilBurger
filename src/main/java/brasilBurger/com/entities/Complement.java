package brasilBurger.com.entities;

import brasilBurger.com.entities.Enum.TypeComplement;
import com.mysql.cj.jdbc.Blob;
import lombok.*;

import java.time.LocalDate;

@Getter
@Setter
@ToString
@NoArgsConstructor
@AllArgsConstructor

public class Complement {
    private int id;
    private String nom;
    private int prix;
    private TypeComplement typeComplement;
    private LocalDate createdAt;
    private String image;

}
