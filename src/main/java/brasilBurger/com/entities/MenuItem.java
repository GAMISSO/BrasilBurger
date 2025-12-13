package brasilBurger.com.entities;

import com.mysql.cj.jdbc.Blob;
import lombok.*;

import java.time.LocalDate;

@Getter
@Setter
@ToString
@NoArgsConstructor
@AllArgsConstructor

public class MenuItem {
    private int id;
    private int BurgerId;
    private Burger burger;
    private int menuId;
    private Menu menu;
    private int complementId;
    private Complement complement;

}
