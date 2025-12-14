package brasilBurger.com.services;

import brasilBurger.com.entities.Burger;
import brasilBurger.com.entities.Complement;

import java.util.List;
import java.util.Optional;

public interface ComplementService {
    /*int createComplement(Complement complement);*/
    List<Complement> getAllComplements();
    /*Optional<Complement> getComplementById(int id);*/
    int deleteComplementById(int id);
    //int updateComplement(Complement complement);
}
