<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = Tour::all();
        $customer = User::where('email', 'customer@test.com')->first();
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin', 'support_agent', 'support_manager']);
        })->get();

        if ($tours->isEmpty() || ! $customer) {
            $this->command->warn('No tours found or customer user not found. Please run TourSeeder and TestUsersSeeder first.');

            return;
        }


        $defaultResponder = $admins->first() ?? User::where('id', '!=', $customer->id)->first() ?? $customer;

        $questions = [
            'en' => [
                'What is included in the tour price?',
                'What should I bring with me?',
                'Is transportation provided?',
                'What is the cancellation policy?',
                'Are meals included?',
                'What is the minimum age requirement?',
                'Is the tour suitable for children?',
                'What languages does the guide speak?',
                'What is the group size?',
                'Are there any physical requirements?',
            ],
            'ar' => [
                'ما الذي يشمله سعر الجولة؟',
                'ماذا يجب أن أحضر معي؟',
                'هل يتم توفير النقل؟',
                'ما هي سياسة الإلغاء؟',
                'هل الوجبات مشمولة؟',
                'ما هو الحد الأدنى للعمر المطلوب؟',
                'هل الجولة مناسبة للأطفال؟',
                'ما هي اللغات التي يتحدث بها المرشد؟',
                'ما هو حجم المجموعة؟',
                'هل هناك أي متطلبات بدنية؟',
            ],
        ];

        $answers = [
            'en' => [
                'The tour price includes transportation, guide services, and entrance fees. Meals and personal expenses are not included.',
                'We recommend bringing comfortable walking shoes, a camera, sunscreen, and a water bottle.',
                'Yes, transportation is included. We provide pickup and drop-off from your hotel.',
                'You can cancel up to 24 hours before the tour for a full refund. Cancellations within 24 hours are subject to a 50% fee.',
                'Breakfast is included. Lunch and dinner are available at additional cost.',
                'The minimum age is 5 years old. Children under 5 are free but must be accompanied by an adult.',
                'Yes, the tour is family-friendly and suitable for children of all ages.',
                'Our guides speak English and Arabic fluently.',
                'The maximum group size is 30 people to ensure a personalized experience.',
                'The tour involves moderate walking. Please inform us if you have any mobility concerns.',
            ],
            'ar' => [
                'يشمل سعر الجولة النقل وخدمات المرشد ورسوم الدخول. الوجبات والمصروفات الشخصية غير مشمولة.',
                'ننصح بإحضار أحذية مريحة للمشي وكاميرا وواقي الشمس وزجاجة ماء.',
                'نعم، النقل مشمول. نوفر الاستلام والتوصيل من فندقك.',
                'يمكنك الإلغاء حتى 24 ساعة قبل الجولة لاسترداد كامل. الإلغاء خلال 24 ساعة يخضع لرسوم 50%.',
                'الإفطار مشمول. الغداء والعشاء متاحان بتكلفة إضافية.',
                'الحد الأدنى للعمر هو 5 سنوات. الأطفال دون 5 سنوات مجاناً ولكن يجب أن يكونوا برفقة شخص بالغ.',
                'نعم، الجولة مناسبة للعائلات ومناسبة للأطفال من جميع الأعمار.',
                'مرشدونا يتحدثون الإنجليزية والعربية بطلاقة.',
                'الحد الأقصى لحجم المجموعة هو 30 شخصاً لضمان تجربة شخصية.',
                'الجولة تتضمن مشياً معتدلاً. يرجى إعلامنا إذا كان لديك أي مخاوف تتعلق بالحركة.',
            ],
        ];

        foreach ($tours as $tour) {

            $questionKeys = array_keys($questions['en']);
            shuffle($questionKeys);
            $selectedKeys = array_slice($questionKeys, 0, 3);

            foreach ($selectedKeys as $index => $key) {
                $isAnswered = $index === 0 || rand(0, 1) === 1;
                $status = $isAnswered ? 'answered' : 'pending';
                $answeringUser = null;
                if ($isAnswered) {
                    if ($admins->isNotEmpty()) {
                        $answeringUser = $admins->random();
                    } else {
                        $answeringUser = $defaultResponder;
                    }
                }

                $question = Question::create([
                    'user_id' => $customer->id,
                    'tour_id' => $tour->id,
                    'status' => $status,
                    'answered_by' => $answeringUser?->id,
                    'answered_at' => $isAnswered ? now()->subDays(rand(1, 10)) : null,
                ]);

                $question->translateOrNew('en')->question = $questions['en'][$key];
                $question->translateOrNew('ar')->question = $questions['ar'][$key];

                if ($isAnswered) {
                    $answerKey = array_rand($answers['en']);
                    $question->translateOrNew('en')->answer = $answers['en'][$answerKey];
                }

                $question->save();
            }
        }

        $this->command->info('Questions seeded successfully!');
    }
}
