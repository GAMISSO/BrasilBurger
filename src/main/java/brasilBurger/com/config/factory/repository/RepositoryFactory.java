package brasilBurger.com.config.factory.repository;

import brasilBurger.com.config.factory.database.DatabaseFactory;
import brasilBurger.com.repositories.Impl.*;

public final class RepositoryFactory {

    private static final PersitanceName persitanceName = PersitanceName.Database;

    private RepositoryFactory() {}

    public static <T> T getInstance(EntityName entityName, Class<T> type) {

        Object repo;

        switch (persitanceName) {
            case Database:
                repo = getRepositoryDatabase(entityName);
                break;
            default:
                throw new IllegalStateException("Persistence non supportée");
        }

        if (repo == null) {
            throw new IllegalStateException(
                    "Repository non trouvé pour " + entityName
            );
        }

        return type.cast(repo);
    }

    private static Object getRepositoryDatabase(EntityName entityName) {
        switch (entityName) {
            case Burger:
                return BurgerRepositoryImpl.getInstance(DatabaseFactory.getInstance());
            case Complement:
                return ComplementRepositoryImpl.getInstance(DatabaseFactory.getInstance());
            case Menu:
                return MenuRepositoryImpl.getInstance(DatabaseFactory.getInstance());
            default:
                throw new IllegalArgumentException("Entity non supportée");
        }
    }
}

