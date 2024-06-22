import cv2
import mediapipe as mp

# تهيئة المتغيرات
cap = cv2.VideoCapture(0)  # كاميرا الويب
mp_drawing = mp.solutions.drawing_utils
mp_pose = mp.solutions.pose

# دالة للكشف عن الملامح باستخدام mediapipe
def detect_face_landmarks(image):
    with mp_pose.Pose(min_detection_confidence=0.5, min_tracking_confidence=0.5) as pose:
        image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        results = pose.process(image_rgb)
        return results.pose_landmarks

# حفظ مواضع الرأس الأولية كمرجع
initial_landmarks = None

# عتبة الحركة المسموح بها
motion_threshold = 0.15  # يمكنك ضبط هذه العتبة حسب الحاجة

# متغير لتتبع حالة الامتحان
exam_closed = False

# دالة لتحليل حركة الرأس
def detect_head_motion(initial, current):
    nose_initial = initial.landmark[mp_pose.PoseLandmark.NOSE]
    nose_current = current.landmark[mp_pose.PoseLandmark.NOSE]

    # حساب المسافة بين موضع الأنف الحالي وموضع الأنف الأولي
    distance = ((nose_current.x - nose_initial.x) ** 2 + (nose_current.y - nose_initial.y) ** 2) ** 0.5

    # إرجاع True إذا كانت حركة الرأس أكبر من العتبة
    return distance > motion_threshold

# الحلقة الرئيسية لمراقبة الامتحان
while True:
    ret, frame = cap.read()
    if not ret:
        break

    # إذا كان الامتحان مغلقًا، نعرض الرسالة فقط ولا نقوم بأي كشف
    if exam_closed:
        cv2.putText(frame, "Exam closed due to head motion", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
    else:
        # الكشف عن الملامح في الإطار
        landmarks = detect_face_landmarks(frame)

        # إذا تم العثور على الملامح
        if landmarks:
            if initial_landmarks is None:
                initial_landmarks = landmarks  # حفظ الملامح الأولية كمرجع

            else:
                motion_detected = detect_head_motion(initial_landmarks, landmarks)
                if motion_detected:
                    exam_closed = True  # إغلاق الامتحان
                    cv2.putText(frame, "Exam closed due to head motion", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
                else:
                    # رسم إطار حول رأس الشخص
                    nose = landmarks.landmark[mp_pose.PoseLandmark.NOSE]
                    x, y = int(nose.x * frame.shape[1]), int(nose.y * frame.shape[0])
                    frame = cv2.rectangle(frame, (x-50, y-50), (x+50, y+50), (255, 0, 0), 2)
                    mp_drawing.draw_landmarks(frame, landmarks, mp_pose.POSE_CONNECTIONS)

    # عرض الإطار المعالج
    cv2.imshow('Exam Monitoring', frame)

    # انتظار الضغط على 'q' لإيقاف البرنامج
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# إغلاق الكاميرا والنوافذ عند انتهاء البرنامج
cap.release()
cv2.destroyAllWindows()
