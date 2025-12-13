package brasilBurger.com.view;

import brasilBurger.com.config.factory.repository.EntityName;
import brasilBurger.com.config.factory.service.ServiceFactory;
import brasilBurger.com.entities.Burger;
import brasilBurger.com.entities.Complement;
import brasilBurger.com.entities.Menu;
import brasilBurger.com.services.BurgerService;
import brasilBurger.com.services.ComplementService;
import brasilBurger.com.services.MenuService;

import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

public class MenuVue extends Vue{
    private final MenuService menuService =
            ServiceFactory.getInstance(EntityName.Menu, MenuService.class);
    private final BurgerService burgerService=
            ServiceFactory.getInstance(EntityName.Burger, BurgerService.class);
    private final ComplementService complementService=
            ServiceFactory.getInstance(EntityName.Complement,ComplementService.class);
    /*
    public void creerMenu(){
        Menu menu =new Menu();
        menu.setNom(saisieChampOblig("Nom du menu: "));
        menu.setDate(LocalDate.now());
        List<Burger> burgers=burgerService.getAllBurgers();
        List<Complement> complements=complementService.getAllComplements();
        BurgerVue burgerVue=new BurgerVue();
        ComplementVue complmentVue=new ComplementVue();
        burgerVue.listerBurger();
        menu.setBurgerId(saisieEntierOblig("Id du Burger: "));
        for(Burger burger:burgers){
            if(menu.getBurgerId()==burger.getId()){
                menu.setBurger(burger);
                break;
            }
        }
        int choice;
        List<Complement> newComplements=new ArrayList<>();
        int sommeTotal=0;
        while(true){
            System.out.println("Voulez vous ajoutez des complement [O/N]: ");
            choice = sc.nextInt();
            if(choice=='O'){
                complmentVue.listerComplement();
                System.out.println("entrez le numero de complement: ");
                int  complement_id=sc.nextInt();
                for(Complement complement:complements){
                    if(complement.getId()==complement_id){
                        newComplements.add(complement);
                        sommeTotal=sommeTotal+complement.getPrix();
                    }
                }
            }if (choice=='N'){
                break;
            }else {
                System.out.println("veuillez ecrire O ou N");
            }
        }
        menu.setPrixTotal(menu.getBurger().getPrix()+sommeTotal);
        menu.setComplements(newComplements);
        menu.setImage(saisieChampOblig("Entrer l'url de l'image: "));
        menuService.createMenu(menu);
    }*/

    public void listerMenu() {
        List<Menu> menus = menuService.getAllMenus();
        for (Menu menu : menus) {
            System.out.print("Id: " + menu.getId());
            System.out.print("Nom: " + menu.getNom());
            System.out.print("Prix: " + menu.getPrixTotal());
            System.out.print("Date: " + menu.getDate());
            System.out.println("============================================================");
        }
    }

    /*
    public void modifierMenu(){
        List<Menu> menus=menuService.getAllMenus();
        System.out.print("Veuillez entrez le numéro du menu qui vous voulez modifier ?");
        int choice=sc.nextInt();
        for (Menu menu:menus){
            if(menu.getId()==choice){
                while(true) {
                    System.out.println("Vous voulez modifier quoi ?!");
                    System.out.println("1-nom");
                    System.out.println("2-Image");
                    System.out.print("Veuillez entrez le numéro de ce que vous vouliez modifier ?");
                    int choice_two = sc.nextInt();
                    if (choice_two == 1) {
                        System.out.println("Entrez le nouveau nom : ");
                        String nom = sc.next();
                        menu.setNom(nom);
                        break;
                    }
                    if (choice_two == 2) {
                        System.out.println("Entrez le nouveau image_url : ");
                        String image = sc.next();
                        menu.setImage(image);
                        break;
                    }
                }
                menuService.updateMenu(menu);
            }
        }

    }*/
}
