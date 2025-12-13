package brasilBurger.com.view;

import brasilBurger.com.config.factory.repository.EntityName;
import brasilBurger.com.config.factory.service.ServiceFactory;
import brasilBurger.com.entities.Burger;
import brasilBurger.com.repositories.BurgerRepository;
import brasilBurger.com.repositories.Impl.BurgerRepositoryImpl;
import brasilBurger.com.services.BurgerService;
import brasilBurger.com.services.Impl.BurgerServiceImpl;

import java.time.LocalDate;
import java.util.List;
import java.util.Scanner;

public class BurgerVue extends Vue {
    private final BurgerService burgerService =
            ServiceFactory.getInstance(EntityName.Burger, BurgerService.class);


    //creer un burger
    public void creerBurger(){
        Burger burger=new Burger();
        burger.setNom(saisieChampOblig("Nom du burger: "));
        burger.setPrix(saisieEntierOblig("Prix du burger: "));
        burger.setCreatedAt(LocalDate.now());
        burger.setImage_url(saisieChampOblig("Entrer l'url de l'image: "));
        burgerService.createBurger(burger);
    }

    /*
    public void listerBurger(){
        List<Burger> burgers=burgerService.getAllBurgers();
        for(Burger burger:burgers){
            System.out.print("Id: "+burger.getId());
            System.out.print("Nom: "+burger.getNom());
            System.out.print("Prix: "+burger.getPrix());
            System.out.print("Date: "+burger.getCreatedAt());
            System.out.println("============================================================");
        }

    }*/

    /*
    public void supprimerBurger(){
        List<Burger> burgers=burgerService.getAllBurgers();
        System.out.print("Veuillez entrez le numéro du burger qui vous voulez supprimer ?");
        int choice=sc.nextInt();
        burgerService.deleteBurgerById(choice);
    }

    //modifier un burger
    public void modifierBurger(){
        List<Burger> burgers=burgerService.getAllBurgers();
        System.out.print("Veuillez entrez le numéro du burger qui vous voulez modifier ?");
        int choice=sc.nextInt();
        for (Burger burger:burgers){
            if(burger.getId()==choice){
                while(true) {
                    System.out.println("Vous voulez modifier quoi ?!");
                    System.out.println("1-nom");
                    System.out.println("2-Prix");
                    System.out.println("3-Image");
                    System.out.print("Veuillez entrez le numéro de ce que vous vouliez modifier ?");
                    int choice_two = sc.nextInt();
                    if (choice_two == 1) {
                        System.out.println("Entrez le nouveau nom : ");
                        String nom = sc.next();
                        burger.setNom(nom);
                        break;
                    }
                    if (choice_two == 2) {
                        System.out.println("Entrez le nouveau prix : ");
                        int prix = sc.nextInt();
                        burger.setPrix(prix);
                        break;
                    }
                    if (choice_two == 3) {
                        System.out.println("Entrez le nouveau image_url : ");
                        String image = sc.next();
                        burger.setImage_url(image);
                        break;
                    }
                }
                burgerService.updateBurger(burger);
            }
        }

    }*/
}
