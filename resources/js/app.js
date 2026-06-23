import './bootstrap';
import DataTable from 'datatables.net-dt';
let table = new DataTable('#miTabla');
import AOS from 'aos';
import 'aos/dist/aos.css';

AOS.init({
  duration: 1000,
  once: false, 
});
