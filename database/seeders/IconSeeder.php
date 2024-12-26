<?php

namespace Database\Seeders;

use App\Models\Icon;
use App\Models\StoreFile;
use App\Models\StoreFolder;
use App\Models\StorePage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $contents = Storage::disk('local')->files('knowledge-base');
        // $files = Storage::disk('local')->files('knowledge-base/files');
        // $contents = array_merge($contents, $files);
        // foreach($contents as $c) {
        //     $icon = new Icon();
        //     $icon->path = "/source-images/$c";
        //     $icon->store_page_id = 34;
        //     $icon->save();
        // }
        // Storage::deleteDirectory('knowledge-base');

        Icon::truncate();
        Icon::insertOrIgnore($this->pathsArray());

        $marketingIcons = [];
        for($i = 67; $i <= 78; $i++) {
            $marketingIcons[] = ['name' => $i.'.png' ,'path' => '/source-images/marketing/'.$i.'.png', 'store_page_id' => 78];
        }
        Icon::insertOrIgnore($marketingIcons);

        Icon::insertOrIgnore([
            'name' => 'Meeting Agenda.png' ,'path' => '/source-images/marketing/Meeting Agenda.png', 'store_page_id' => 78
        ]);
        Icon::insertOrIgnore([
            'name' => 'Minutes.png' ,'path' => '/source-images/marketing/Minutes.png', 'store_page_id' => 78
        ]);

        $marketingIcons = [];
        for($i = 95; $i <= 98; $i++) {
            $marketingIcons[] = ['name' => $i.'.png' ,'path' => '/source-images/marketing/'.$i.'.png', 'store_page_id' => 78];
        }
        Icon::insertOrIgnore($marketingIcons);

        $hrIcons = [];
        for($i = 1; $i <= 7; $i++) {
            $hrIcons[] = ['name' => $i.'.png' ,'path' => '/source-images/hr-hub/'.$i.'.png', 'store_page_id' => 90];
        }
        Icon::insertOrIgnore($hrIcons);

        // $pageIds = StorePage::where('parent_id', 34)->pluck('id');
        // $folders = StoreFolder::whereIn('page_id', $pageIds)
        //     ->whereNull('icon_path')
        //     ->update([
        //         'icon_path' => '/source-images/knowledge-base/All Files.png',
        //         'background_color' => '#fcd0b2',
        //         'border_color' => '#fcd0b2',
        //         'text_color' => '#af5a20',
        //     ]);

        // $folderIds = StoreFolder::whereIn('page_id', $pageIds)->pluck('id');

        // $files = StoreFile::whereIn('folder_id', $folderIds)
        //     ->update([
        //         'icon_path' => '/source-images/knowledge-base/Default.png',
        //         'background_color' => '#e2acdf',
        //         'border_color' => '#e2acdf',
        //         'text_color' => 'black',
        //     ]);
    }

    private function pathsArray()
    {
        return [
            ['name' => 'All Files.png', 'path' => '/source-images/knowledge-base/All Files.png', 'store_page_id' => 34],
            ['name' => 'Board of Pharmacy.png', 'path' => '/source-images/knowledge-base/Board of Pharmacy.png', 'store_page_id' => 34],
            ['name' => 'How To Guide.png', 'path' => '/source-images/knowledge-base/How To Guide.png', 'store_page_id' => 34],
            ['name' => 'P&P.png', 'path' => '/source-images/knowledge-base/P&P.png', 'store_page_id' => 34],
            ['name' => 'Pharmacy Forms.png', 'path' => '/source-images/knowledge-base/Pharmacy Forms.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe).png', 'path' => '/source-images/knowledge-base/Process Documents (Scribe).png', 'store_page_id' => 34],
            ['name' => 'SOP.png', 'path' => '/source-images/knowledge-base/SOP.png', 'store_page_id' => 34],
            ['name' => 'AR Aging Report.png', 'path' => '/source-images/knowledge-base/files/AR Aging Report.png', 'store_page_id' => 34],
            ['name' => 'AR Manual Reconciliation.png', 'path' => '/source-images/knowledge-base/files/AR Manual Reconciliation.png', 'store_page_id' => 34],
            ['name' => 'Accessing and Navigating MGMT88 Portal and Dashboard Instructions.png', 'path' => '/source-images/knowledge-base/files/Accessing and Navigating MGMT88 Portal and Dashboard Instructions.png', 'store_page_id' => 34],
            ['name' => 'Adding Medication Favorites.png', 'path' => '/source-images/knowledge-base/files/Adding Medication Favorites.png', 'store_page_id' => 34],
            ['name' => 'Assigning Correct Work Location for Employees.png', 'path' => '/source-images/knowledge-base/files/Assigning Correct Work Location for Employees.png', 'store_page_id' => 34],
            ['name' => 'Checking Replies to Therapeutic Changes.png', 'path' => '/source-images/knowledge-base/files/Checking Replies to Therapeutic Changes.png', 'store_page_id' => 34],
            ['name' => 'Claim Reversal Via Rx Queue Workflow.png', 'path' => '/source-images/knowledge-base/files/Claim Reversal Via Rx Queue Workflow.png', 'store_page_id' => 34],
            ['name' => 'Compliance and Regulatory Affairs Introduction Pharmacies.png', 'path' => '/source-images/knowledge-base/files/Compliance and Regulatory Affairs Introduction Pharmacies.png', 'store_page_id' => 34],
            ['name' => 'DEFAULT How To Guide.png', 'path' => '/source-images/knowledge-base/files/DEFAULT How To Guide.png', 'store_page_id' => 34],
            ['name' => 'DEFAULT P&P.png', 'path' => '/source-images/knowledge-base/files/DEFAULT P&P.png', 'store_page_id' => 34],
            ['name' => 'DEFAULT Process Documents (Scribe).png', 'path' => '/source-images/knowledge-base/files/DEFAULT Process Documents (Scribe).png', 'store_page_id' => 34],
            ['name' => 'DEFAULT SOP.png', 'path' => '/source-images/knowledge-base/files/DEFAULT SOP.png', 'store_page_id' => 34],
            ['name' => 'Design Birthday Announcements Creatives.png', 'path' => '/source-images/knowledge-base/files/Design Birthday Announcements Creatives.png', 'store_page_id' => 34],
            ['name' => 'Design Healthcare Season Creatives.png', 'path' => '/source-images/knowledge-base/files/Design Healthcare Season Creatives.png', 'store_page_id' => 34],
            ['name' => 'FWA Binder Spine.png', 'path' => '/source-images/knowledge-base/files/FWA Binder Spine.png', 'store_page_id' => 34],
            ['name' => 'FWA Track Binder Cover.png', 'path' => '/source-images/knowledge-base/files/FWA Track Binder Cover.png', 'store_page_id' => 34],
            ['name' => 'How To Guide-MGMT Clinical Workflow.png', 'path' => '/source-images/knowledge-base/files/How To Guide-MGMT Clinical Workflow.png', 'store_page_id' => 34],
            ['name' => 'How To Guide.png', 'path' => '/source-images/knowledge-base/files/How To Guide.png', 'store_page_id' => 34],
            ['name' => 'Inventory Control Cycle Count (2).png', 'path' => '/source-images/knowledge-base/files/Inventory Control Cycle Count (2).png', 'store_page_id' => 34],
            ['name' => 'Inventory Control Cycle Count.png', 'path' => '/source-images/knowledge-base/files/Inventory Control Cycle Count.png', 'store_page_id' => 34],
            ['name' => 'MGMT FAQs Compliance FWA Requirements Pharmacy.png', 'path' => '/source-images/knowledge-base/files/MGMT FAQs Compliance FWA Requirements Pharmacy.png', 'store_page_id' => 34],
            ['name' => 'MGMT FDR Requirements Pharmacy.png', 'path' => '/source-images/knowledge-base/files/MGMT FDR Requirements Pharmacy.png', 'store_page_id' => 34],
            ['name' => 'MGMT Fraud and Abuse Reporting Form Pharmacy.png', 'path' => '/source-images/knowledge-base/files/MGMT Fraud and Abuse Reporting Form Pharmacy.png', 'store_page_id' => 34],
            ['name' => 'P&P-FWA.png', 'path' => '/source-images/knowledge-base/files/P&P-FWA.png', 'store_page_id' => 34],
            ['name' => 'P&P-General.png', 'path' => '/source-images/knowledge-base/files/P&P-General.png', 'store_page_id' => 34],
            ['name' => 'P&P-Hazardous Drugs - SDS.png', 'path' => '/source-images/knowledge-base/files/P&P-Hazardous Drugs - SDS.png', 'store_page_id' => 34],
            ['name' => 'P&P-Prescription Denial Binder.png', 'path' => '/source-images/knowledge-base/files/P&P-Prescription Denial Binder.png', 'store_page_id' => 34],
            ['name' => 'P&P-RTS Binder.png', 'path' => '/source-images/knowledge-base/files/P&P-RTS Binder.png', 'store_page_id' => 34],
            ['name' => 'P&P-SOP Binders.png', 'path' => '/source-images/knowledge-base/files/P&P-SOP Binders.png', 'store_page_id' => 34],
            ['name' => 'P&P-Visitor Log-in Binder.png', 'path' => '/source-images/knowledge-base/files/P&P-Visitor Log-in Binder.png', 'store_page_id' => 34],
            ['name' => 'P&P.png', 'path' => '/source-images/knowledge-base/files/P&P.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe)-Clinical.png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe)-Clinical.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe)-Marketing.png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe)-Marketing.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe)-Operations.png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe)-Operations.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe)-Procurement & Finance.png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe)-Procurement & Finance.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe)-Project Management.png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe)-Project Management.png', 'store_page_id' => 34],
            ['name' => 'Process Documents (Scribe).png', 'path' => '/source-images/knowledge-base/files/Process Documents (Scribe).png', 'store_page_id' => 34],
            ['name' => 'Responding to Rx Refill Requests SOP.png', 'path' => '/source-images/knowledge-base/files/Responding to Rx Refill Requests SOP.png', 'store_page_id' => 34],
            ['name' => 'SOP-General.png', 'path' => '/source-images/knowledge-base/files/SOP-General.png', 'store_page_id' => 34],
            ['name' => 'SOP-Inventory.png', 'path' => '/source-images/knowledge-base/files/SOP-Inventory.png', 'store_page_id' => 34],
            ['name' => 'SOP-Ordering SOP.png', 'path' => '/source-images/knowledge-base/files/SOP-Ordering SOP.png', 'store_page_id' => 34],
            ['name' => 'SOP-Vaccination.png', 'path' => '/source-images/knowledge-base/files/SOP-Vaccination.png', 'store_page_id' => 34],
            ['name' => 'SOP.png', 'path' => '/source-images/knowledge-base/files/SOP.png', 'store_page_id' => 34],
            ['name' => 'Scheduling Covid Vaccine.png', 'path' => '/source-images/knowledge-base/files/Scheduling Covid Vaccine.png', 'store_page_id' => 34],
            ['name' => 'TRP Cash Programs SOP.png', 'path' => '/source-images/knowledge-base/files/TRP Cash Programs SOP.png', 'store_page_id' => 34],
            ['name' => 'Telephonic Interpreting Service SOP.png', 'path' => '/source-images/knowledge-base/files/Telephonic Interpreting Service SOP.png', 'store_page_id' => 34],
            ['name' => 'Vaccination Report.png', 'path' => '/source-images/knowledge-base/files/Vaccination Report.png', 'store_page_id' => 34],
            ['name' => 'Vaccination SOP.png', 'path' => '/source-images/knowledge-base/files/Vaccination SOP.png', 'store_page_id' => 34],
            ['name' => 'Waste Management & Pharmacy Supply Ordering SOP (2).png', 'path' => '/source-images/knowledge-base/files/Waste Management & Pharmacy Supply Ordering SOP (2).png', 'store_page_id' => 34],
            ['name' => 'Waste Management & Pharmacy Supply Ordering SOP.png', 'path' => '/source-images/knowledge-base/files/Waste Management & Pharmacy Supply Ordering SOP.png', 'store_page_id' => 34],
            ['name' => 'Year End Inventory Practices (2).png', 'path' => '/source-images/knowledge-base/files/Year End Inventory Practices (2).png', 'store_page_id' => 34],
            ['name' => 'Year End Inventory Practices.png', 'path' => '/source-images/knowledge-base/files/Year End Inventory Practices.png', 'store_page_id' => 34],
            ['name' => 'Default.png', 'path' => '/source-images/knowledge-base/Default.png', 'store_page_id' => 34],
        ];
    }
}
