package brasilBurger.com.services.Impl;

import brasilBurger.com.entities.Complement;
import brasilBurger.com.repositories.BurgerRepository;
import brasilBurger.com.repositories.ComplementRepository;
import brasilBurger.com.services.BurgerService;
import brasilBurger.com.services.ComplementService;

import java.util.List;
import java.util.Optional;

public class ComplementServiceImpl implements ComplementService {
    private ComplementRepository complementRepository;
    private static ComplementService instance=null;
    public static ComplementServiceImpl getInstance(ComplementRepository complementRepository) {
        if (instance == null) {
            instance = new ComplementServiceImpl(complementRepository);
        }
        return (ComplementServiceImpl) instance;
    }

    private  ComplementServiceImpl(ComplementRepository complementRepository) {
        this.complementRepository = complementRepository;
    }
    /*
    @Override
    public int createComplement(Complement complement) {
        return complementRepository.insert(complement);
    }*/
    @Override
    public List<Complement> getAllComplements() {
        return complementRepository.selectAll();
    }
    /*
    @Override
    public Optional<Complement> getComplementById(int id) {
        List<Complement> complements = complementRepository.selectAll();
        for (Complement complement : complements) {
            if (complement.getId() == id) {
                return Optional.of(complement);
            }
        }
        return Optional.empty();
    }*/

    @Override
    public int deleteComplementById(int id) {
        List<Complement> complements = complementRepository.selectAll();
        for (Complement complement : complements) {
            if (complement.getId() == id) {
                complementRepository.delete(complement);
                return complementRepository.selectAll().size();
            }
        }
        return 0;
    }
    /*
    @Override
    public int updateComplement(Complement complement) {
        return complementRepository.update(complement);
    }*/
}
