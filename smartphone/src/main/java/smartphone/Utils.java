package smartphone;

import java.io.Console;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Scanner;

import javax.swing.BoxLayout;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;

public class Utils {

	private Utils() {
	}

	/**
	 * I/O stuff
	 */

	/**
	 * This method contemplates if the program is being run in Eclipse or not, as
	 * Eclipse does not support System.console. Same for Gradle.
	 */
	public static void println(String str) {

		Console c = System.console();
		
		if (c == null) {
			JOptionPane.showMessageDialog(null, str);
		} else {
			System.out.println(str);
		}
	}

	/**
	 * This method contemplates if the program is being run in Eclipse or not, as
	 * Eclipse does not support System.console. Same for Gradle.
	 */
	public static String readString(String prompt) {

		String input = "";
		Console c = System.console();

		if (c == null) {
			input = JOptionPane.showInputDialog(prompt);
		} else {
			println(prompt);
			input = c.readLine();
		}
		return input;
	}

	/**
	 * This method contemplates if the program is being run in Eclipse or not, as
	 * Eclipse does not support System.console. Same for Gradle.
	 */
	public static String readPassword(String prompt) {

		String input = "";
		Console c = System.console();

		if (c == null) {
			// https://stackoverflow.com/questions/8881213/joptionpane-to-get-password
			JPanel panel = new JPanel();
			panel.setLayout(new BoxLayout(panel, BoxLayout.PAGE_AXIS));
			// https://stackoverflow.com/questions/1090098/newline-in-jlabel
			prompt = "<html>" + prompt + "</html><br />";
			prompt = prompt.replaceAll("\n", "<br />");
			JLabel label = new JLabel(prompt);
			JPasswordField pass = new JPasswordField(10);
			panel.add(label);
			panel.add(pass);
			String[] options = new String[]{"OK", "Cancel"};
			int option = JOptionPane.showOptionDialog(null, panel, "Enter password",
			                         JOptionPane.NO_OPTION, JOptionPane.PLAIN_MESSAGE,
			                         null, options, options[0]);
			if(option == 0) // pressing OK button
			{
			    char[] password = pass.getPassword();
			    input = new String(password);
			}
			
			// Old solution (reads plain text)
			//input = JOptionPane.showInputDialog(prompt);
		} else {
			char[] in = System.console().readPassword(prompt);
			input = String.valueOf(in);
		}
		return input;
	}

	public static int readInt(String prompt) {
		int i;
		while (true) {
			try {
				i = Integer.parseInt(readString(prompt));
				break;
			} catch (NumberFormatException e) {
				println("Not a valid number.");
			}
		}
		return i;
	}

	public static int readIntFromFile(String filename) {
		int i = -1;
		try (FileInputStream fis = new FileInputStream(filename); Scanner scanner = new Scanner(fis);) {
			i = scanner.nextInt();
		} catch (IOException e) {
			e.printStackTrace();
			System.out.println("Could not read from file '" + filename + "'.");
			System.exit(0);
		}
		return i;
	}
}