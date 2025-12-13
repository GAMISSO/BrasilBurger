package brasilBurger.com.config.factory.service;

import brasilBurger.com.config.factory.repository.EntityName;
import brasilBurger.com.config.factory.repository.RepositoryFactory;
import brasilBurger.com.entities.Complement;
import brasilBurger.com.repositories.*;
import brasilBurger.com.services.*;
import brasilBurger.com.services.Impl.*;

public final class ServiceFactory {

    private ServiceFactory() {}

    public static <T> T getInstance(EntityName entityName, Class<T> type) {

        Object service;

        switch (entityName) {
            case Burger:
                service = BurgerServiceImpl.getInstance(
                        RepositoryFactory.getInstance(EntityName.Burger, BurgerRepository.class)
                );
                break;

            case Complement:
                service = ComplementServiceImpl.getInstance(
                        RepositoryFactory.getInstance(EntityName.Complement, ComplementRepository.class)
                );
                break;

            case Menu:
                service = MenuServiceImpl.getInstance(
                        RepositoryFactory.getInstance(EntityName.Menu, MenuRepository.class)
                );
                break;

            default:
                throw new IllegalArgumentException("Service non supporté");
        }

        return type.cast(service);
    }
}

