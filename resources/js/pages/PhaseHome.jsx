import { Link } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
export default function PhaseHome({phase}) {
 return <AppLayout title={`BiliSkwela · Phase ${phase}`} subtitle="A working checkpoint of the six-phase project.">
 <section className="hero"><div><span className="badge cream">BUILDING THE FOUNDATION</span><h2>May gamit.<br/>May galing.</h2><p>Current checkpoint: Phase {phase}. Open the available modules in the sidebar.</p></div><div className="hero-art"><span className="notebook">B<span>READY TO LEARN</span></span><span className="pencil">✎</span></div></section>
 <section className="card" style={{marginTop:22}}><h2>What is available?</h2><p>Phase 1: schema, models, shared React UI and basic sign-in.</p>{phase>=2 && <p>Phase 2: category and supplier CRUD.</p>}{phase>=3 && <p>Phase 3: products, stock movements and history.</p>}{phase>=4 && <p>Phase 4: registration, password recovery and account settings.</p>}{phase>=5 && <p>Phase 5: action permissions and team role management.</p>}<p>The final dashboard and printable reports arrive in Phase 6.</p></section>
 </AppLayout>;
}
