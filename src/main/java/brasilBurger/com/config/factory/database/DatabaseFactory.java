package brasilBurger.com.config.factory.database;

import brasilBurger.com.config.database.Database;
import brasilBurger.com.config.database.DatabaseImpl;

public final class DatabaseFactory {
    private static final SGDBName sgbdName = SGDBName.POSTGRESQL;

    private DatabaseFactory() {
    }

    public static Database getInstance() {
        return DatabaseImpl.getInstance(EntityManager.persistanceUnit(sgbdName));
    }
}
