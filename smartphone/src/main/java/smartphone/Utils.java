package smartphone;

import java.io.Console;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Scanner;

import javax.swing.JOptionPane;

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
			input = JOptionPane.showInputDialog(prompt);
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